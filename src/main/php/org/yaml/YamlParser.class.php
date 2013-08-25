<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  protected function matching($reader, $value, $begin, $end) {
    while (false === ($s= strrpos($value, $end))) {
      if (null === ($line= $reader->nextLine())) {
        throw new \lang\FormatException('Unmatched "'.$begin.'", encountered EOF');
      }
      $value.= $line;
    }
    $offset= strlen($begin);
    return substr($value, $offset, $s - $offset);
  }

  /**
   * Expand escapes sequences inside a string
   *
   * @param  string $value
   * @return string
   */
  protected function expand($value) {
    static $escapes= array('r' => "\x0d", 'n' => "\x0a", 't' => "\x09", 'b' => "\x08");

    $r= '';
    for ($i= 0, $l= strlen($value); $i < $l; $i++) {
      if ('\\' === $value{$i}) {
        $e= $value{$i + 1};
        if (isset($escapes[$e])) {
          $r.= $escapes[$e];
          $i+= 1;
        } else if ('x' === $e) {
          $r.= chr(hexdec(substr($value, $i + 2, 2)));
          $i+= 3;
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
   * @return var
   */
  protected function valueOf($reader, $value) {
    if ('true' === $value) {
      return true;
    } else if ('false' === $value) {
      return false;
    } else if ("'" === $value{0}) {
      return substr($value, 1, -1);
    } else if ('"' === $value{0}) {
      return $this->expand(substr($value, 1, -1));
    } else if ('{' === $value{0}) {     // Flow style map
      $matching= $this->matching($reader, $value, '{', '}');
      $l= strlen($matching);
      $r= array();
      $o= 0;
      while ($o < $l) {
        $s= strcspn($matching, ',', $o);
        $token= trim(substr($matching, $o, $s), ' ');
        $key= $value= null;
        sscanf($token, "%[^:]: %[^\r]", $key, $value);
        $r[$key]= $this->valueOf($reader, $value);
        $o+= $s + 1;
      }
      return $r;
    } else if ('[' === $value{0}) {
      $matching= $this->matching($reader, $value, '[', ']');
      $l= strlen($matching);
      $r= array();
      $o= 0;
      while ($o < $l) {
        $s= strcspn($matching, ',', $o);
        $token= trim(substr($matching, $o, $s), ' ');
        $r[]= $this->valueOf($reader, $token);
        $o+= $s + 1;
      }
      return $r;
    } else if ('&' === $value{0}) {
      if (false === ($o= strpos($value, ' '))) {
        return $this->identifiers[$value]= null;
      } else {
        $id= substr($value, 1, $o - 1);
        return $this->identifiers[$id]= substr($value, $o + 1);
      }
    } else if ('*' === $value{0}) {
      return $this->identifiers[substr($value, 1)];
    } else if ('0o' === substr($value, 0, 2)) {
      return octdec(substr($value, 2));
    } else if ('0x' === substr($value, 0, 2)) {
      return hexdec(substr($value, 2));
    } else if (preg_match('/^[+-]?[0-9]+$/', $value)) {
      return (int)$value;
    } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+(e\+[0-9]+)?$/', $value)) {
      return (double)$value;
    } else if ('>' === $value{strlen($value)- 1}) {
      $r= '';
      $next= $reader->nextLine();
      $spaces= strspn($next, ' ');
      do {
        $r.= ' '.substr($next, $spaces);
        $indent= strspn($next, ' ');
      } while (($indent === $spaces) && null !== ($next= $reader->nextLine()));
      return substr($r, 1);
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
    $r= array();
    $id= 0;
    $this->identifiers= array();
    while (null !== ($line= $reader->nextLine())) {

      // Everything after the comment is ignored
      if (false !== ($comment= strrpos($line, '#'))) {
        $line= rtrim(substr($line, 0, $comment), ' ');
      }

      // Indentation gives structure
      $spaces= strspn($line, ' ');
      if ($spaces === strlen($line)) {
        continue;
      } else if ($spaces > $level) {    // indent
        $reader->resetLine($line);
        $r[$key]= $this->parse($reader, $level + $spaces);
        continue;
      } else if ($spaces < $level) {    // dedent
        $reader->resetLine($line);
        break;
      }

      // Sequences (begin with a dash) and maps (key: value)
      if ('-' === $line{$level}) {
        $key= $id++;

        if (strpos($line, ':')) {
          $reader->resetLine(str_repeat(' ', $level + 2).substr($line, $level + 2));
        } else {
          $r[$key]= $this->valueOf($reader, substr($line, $level + 2));
        }
      } else if (strpos($line, ':')) {
        $key= $value= null;
        sscanf($line, "%[^:]: %[^\r]", $key, $value);
        $key= trim(substr($key, $level), ' ');
        $r[$key]= $this->valueOf($reader, $value);
      } else {
        throw new \lang\FormatException('Unparseable line "'.$line.'"');
      }
    }
    return $r;
  }
}