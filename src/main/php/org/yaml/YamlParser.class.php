<?php namespace org\yaml;

class YamlParser extends \lang\Object {
  protected $constructors= [];
  protected static $literals;

  static function __static() {
    self::$literals= [
      'null'  => null,  'Null'  => null,  'NULL' => null, '~' => null,
      'true'  => true,  'True'  => true,  'TRUE' => true,
      'false' => false, 'False' => false, 'FALSE' => false,
      '.nan'  => NAN,   '.NaN'  => NAN,   '.NAN' => NAN,
      '-.inf' => -INF,  '-.Inf' => -INF,  '-.INF' => -INF,
      '+.inf' => INF,   '+.Inf' => INF,   '+.INF' => INF,
      '.inf'  => INF,   '.Inf'  => INF,   '.INF' => INF
    ];
  }

  /**
   * Creates a new instance of the YAML parser
   */
  public function __construct() {

    // Initialize default tag handlers: str, null, int, float and bool
    $literals= self::$literals;
    $this->constructors['str']= function($in) { return (string)$in; };
    $this->constructors['null']= function($in) { return null; };
    $this->constructors['int']= function($in) { 
      if (0 === strncmp('0o', $in, 2)) {
        return octdec(substr($in, 2));
      } else if (0 === strncmp('0x', $in, 2)) {
        return hexdec(substr($in, 2));
      } else {
        return (int)$in;
      }
    };
    $this->constructors['float']= function($in) use($literals) {
      return (float)(isset($literals[$in]) ? $literals[$in] : $in);
    };
    $this->constructors['bool']= function($in) use($literals) { 
      return (bool)(isset($literals[$in]) ? $literals[$in] : $in);
    };
    $this->constructors['seq']= function($in, $reader, $parser) {
      if ('[' === $in{0}) {
        $r= [];
        $seq= $reader->matching($in, '[', ']');
        while ($token= $reader->nextToken($seq)) {
          $r[]= $parser->valueOf($reader, $token);
        }
        return $r;
      } else {
        throw new \lang\FormatException('Unsupported sequence style');
      }
    };
    $this->constructors['map']= function($in, $reader, $parser) {
      if ('{' === $in{0}) {
        $r= [];
        $map= $reader->matching($in, '{', '}');
        while ($token= $reader->nextToken($map)) {
          $r[$parser->valueOf($reader, $token)]= $parser->valueOf($reader, $reader->nextToken($map));
        }
        return $r;
      } else {
        throw new \lang\FormatException('Unsupported mapping style');
      }
    };
  }

  protected function indented($reader, $join) {
    $r= '';
    $next= $reader->nextLine();
    $spaces= strspn($next, ' ');
    do {
      if (strspn($next, ' ') < $spaces) break;
      $r.= $join.substr($next, $spaces);
    } while (null !== ($next= $reader->nextLine()));
    $reader->resetLine($next);
    return substr($r, 1);
  }

  /**
   * Expand escapes sequences inside a string
   *
   * @see    http://www.yaml.org/spec/1.2/spec.html#id2776092 - "5.7. Escaped Characters"
   * @param  string $value
   * @return string
   * @throws lang.FormatException for illegal escape sequences
   */
  protected function expand($value) {
    static $escapes= [
      '0' => "\x00", 'a' => "\x07", 'b' => "\x08", 't' => "\x09",
      'n' => "\x0a", 'v' => "\x0b", 'f' => "\x0c", 'r' => "\x0d",
      'e' => "\x1b", '\\' => '\\', '"' => '"', '/' => '/', ' ' => ' '
    ];

    $r= '';
    for ($i= 0, $l= strlen($value); $i < $l; $i++) {
      if ('\\' === $value{$i}) {
        $e= $value{$i + 1};
        if (isset($escapes[$e])) {
          $r.= $escapes[$e];
          $i+= 1;
        } else if ('x' === $e && 1 === sscanf(substr($value, $i + 2, 2), '%2x', $v)) {
          $r.= chr($v);
          $i+= 3;
        } else {
          throw new \lang\FormatException('Illegal escape sequence starting with \\'.$e);
        }
      } else {
        $r.= $value{$i};
      }
    }
    return $r;
  }

  /**
   * Parse a value
   *
   * @param  org.yaml.Input $reader
   * @param  string $value
   * @param  int $level
   * @return var
   */
  public function valueOf($reader, $value, $level= 0) {
    if (null === $value) {
      if (null === ($line= $reader->nextLine())) return null;  // EOF

      // Check whether the next line at same or lesser indentation level. This
      // means we have a line like "key:\n" which means we're encountering an 
      // empty value, which means NULL. If we find elements indented at the same
      // level, they're either a sequence (- value) or a map (key: value).
      $spaces= strspn($line, ' ');
      if ($spaces < $level) {
        $reader->resetLine($line);
        return null;
      }

      $r= [];
      $id= 0;
      do {
        $p= strspn($line, ' ');
        $l= strlen($line);
        if ($p < $spaces) {
          break;
        } else if ($p === $l) {
          continue;
        } else if ('#' === $line{$spaces}) {
          continue;
        }

        // Sequences (begin with a dash) and maps (key: value). A special case is the 
        // compacted mapping, where the first key starts right inside a sequence, e.g.
        // "- one: two\n  three: four"
        if ('-' === $line{$spaces}) {
          $key= $id++;

          if (strpos($line, ': ')) {
            $reader->resetLine(str_repeat(' ', $spaces + 2).substr($line, $spaces + 2));
            $r[$key]= $this->valueOf($reader, null, $spaces);
          } else if ($spaces + 2 > $l) {
            $r[$key]= $this->valueOf($reader, null, $spaces);
          } else {
            $r[$key]= $this->valueOf($reader, substr($line, $spaces + 2), $spaces);
          }
        } else if (!strpos('#!?{[', $line{$spaces}) && strpos($line, ':')) {
          $key= $value= null;
          sscanf($line, "%[^:]: %[^\r]", $key, $value);
          $key= trim(substr($key, $spaces), ' ');
          $r[$key]= $this->valueOf($reader, $value, $spaces);
        } else {
          $r= $this->valueOf($reader, $line, $spaces);
        }

      } while (null !== ($line= $reader->nextLine()));
      $reader->resetLine($line);
      return $r;
    }

    // Everything after the comment is ignored
    if (false !== ($comment= strrpos($value, '#'))) {
      $value= rtrim(substr($value, 0, $comment), ' ');
    }

    // Resolve references
    if ('&' === $value{0}) {
      if (false === ($o= strpos($value, ' '))) {
        $id= substr($value, 1);
        return $this->identifiers[$id]= $this->valueOf($reader, null, $level);
      } else {
        $id= substr($value, 1, $o - 1);
        return $this->identifiers[$id]= $this->valueOf($reader, substr($value, $o + 1), $level);
      }
    } else if ('*' === $value{0}) {
      $id= substr($value, 1);
      if (!isset($this->identifiers[$id])) {
        throw new \lang\IllegalArgumentException(sprintf(
          'Unresolved reference "%s", have ["%s"]',
          $id,
          implode('", "', array_keys($this->identifiers))
        ));
      }
      return $this->identifiers[$id];
    }

    // Now check for tags starting with !!, finally match based on patterns
    if (0 === strncmp('!!', $value, 2)) {
      $p= strcspn($value, ' ', 2);
      $constructor= substr($value, 2, $p);
      if (!isset($this->constructors[$constructor])) {
        throw new \lang\IllegalArgumentException('Cannot construct '.$constructor);
      }
      return $this->constructors[$constructor](substr($value, $p + 2 + 1), $reader, $this);
    } else if ("'" === $value{0}) {
      return substr($value, 1, -1);
    } else if ('"' === $value{0}) {
      return $this->expand(substr($value, 1, -1));
    } else if ('{' === $value{0}) {     // Flow style map
      return $this->constructors['map']($value, $reader, $this);
    } else if ('[' === $value{0}) {
      return $this->constructors['seq']($value, $reader, $this);
    } else if (0 === strncmp('0o', $value, 2)) {
      return octdec(substr($value, 2));
    } else if (0 === strncmp('0x', $value, 2)) {
      return hexdec(substr($value, 2));
    } else if (array_key_exists((string)$value, self::$literals)) {
      return self::$literals[$value];
    } else if (preg_match('/^[+-]?[0-9]+$/', $value)) {
      return (int)$value;
    } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+(e\+[0-9]+)?$/', $value)) {
      return (double)$value;
    } else if (preg_match('/^[0-9]{4}\-[0-9]{2}-[0-9]{2}/', $value)) {
      return new \util\Date($value);
    } else if ('>' === $value{strlen($value)- 1}) {
      return $this->indented($reader, ' ');
    } else if ('|' === $value{strlen($value)- 1}) {
      return $this->indented($reader, "\n");
    } else {
      return $value;
    }
  }

  /**
   * Parse a given input source
   *
   * @param  org.yaml.Input $reader
   * @param  int $level
   * @return var
   */
  public function parse($reader, $level= 0) {
    $this->identifiers= [];
    return $this->valueOf($reader, null, 0);
  }
}