<?php namespace org\yaml;

use lang\FormatException;

abstract class Input {
  const CLIP = '', STRIP = '-', KEEP = '+';

  private static $literals= [
    'null'  => null,  'Null'  => null,  'NULL' => null, '~' => null,
    'true'  => true,  'True'  => true,  'TRUE' => true,
    'false' => false, 'False' => false, 'FALSE' => false,
    '.nan'  => NAN,   '.NaN'  => NAN,   '.NAN' => NAN,
    '-.inf' => -INF,  '-.Inf' => -INF,  '-.INF' => -INF,
    '+.inf' => INF,   '+.Inf' => INF,   '+.INF' => INF,
    '.inf'  => INF,   '.Inf'  => INF,   '.INF' => INF
  ];
  private $stack= [];

  /**
   * Reads a line
   *
   * @return string or NULL to indicate EOF
   */
  protected abstract function readLine();

  /**
   * Rewind this input to the beginning
   *
   * @return void
   */
  public abstract function rewind();

  /**
   * Pushes a given line back onto the stack.
   *
   * @param  string $l
   */
  public function resetLine($l) {
    $this->stack[]= $l;
  }

  /**
   * Returns the next line, either from the stack or from the underlying
   * reader. Returns NULL to indicate EOF.
   *
   * @return string
   */
  public function nextLine() {
    if ($this->stack) {
      return array_shift($this->stack);
    }
    return $this->readLine();
  }

  /**
   * Returns token inside a given string, or NULL.
   *
   * @param  string $in
   * @return var[]
   */
  public function tokenIn($in) {
    $start= 0;
    return $this->token($in, $start);
  }

  /**
   * Produces the next indented token, reading as many lines as necessary
   *
   * @see    https://yaml-multiline.info/
   * @param  string $join
   * @param  string $mode
   * @return string
   * @throws lang.FormatException if unknown chomping mode is given
   */
  private function indented($join, $mode) {
    $r= '';
    $next= $this->nextLine();
    $spaces= strspn($next, ' ');
    do {
      if ('' === trim($next)) {
        $r.= "\n";
        continue;
      } else if (strspn($next, ' ') < $spaces) {
        $this->resetLine($next);
        break;
      } else {
        $r.= $join.substr($next, $spaces);
      }
    } while (null !== ($next= $this->nextLine()));

    switch ($mode) {
      case self::KEEP: return substr($r, 1);
      case self::STRIP: return rtrim(substr($r, 1), "\r\n");
      case self::CLIP: return rtrim(substr($r, 1), "\r\n")."\n";
      default: throw new FormatException('Unknown chomping mode "'.$mode.'"');
    }
  }

  /**
   * Handles multiline quoted strings
   *
   * @see    https://yaml-multiline.info/
   * @param  string $context
   * @return string
   * @throws lang.FormatException if EOF is encountered
   */
  private function lines($context) {
    $lines= '';
    do {
      $line= $this->nextLine();
      if (null === $line) {
        throw new FormatException('Unclosed '.$context.' quote, encountered EOF');
      }

      $line= ltrim($line);
      if ('' !== $line) {
        return '' === $lines ? ' '.$line : $lines.$line;
      }
      $lines.= "\n";
    } while (true);
    // Unreachable
  }

  /**
   * Produces the next token, continuiung on the next line if necessary
   *
   * @param  string $in
   * @param  int $offset
   * @param  string $context
   * @return var[]
   */
  private function flow(&$in, &$offset, $context) {
    static $end= ['sequence' => ',]#', 'map' => ':,}#'];

    if ($offset >= strlen($in)) {
      $line= $this->nextLine();
      if (null === $line) throw new FormatException('Encountered EOF while parsing '.$context);
      $in.= $line;
    }
    return $this->token($in, $offset, $end[$context]);
  }

  /**
   * Handles escape sequences
   *
   * @see    http://www.yaml.org/spec/1.2/spec.html#id2776092 - "5.7. Escaped Characters"
   * @param  string $in
   * @param  int $offset
   * @return string
   * @throws lang.FormatException
   */
  private function escape($in, &$offset) {
    static $escapes= [
      '0' => "\x00", 'a' => "\x07", 'b' => "\x08", 't' => "\x09",
      'n' => "\x0a", 'v' => "\x0b", 'f' => "\x0c", 'r' => "\x0d",
      'e' => "\x1b", '\\' => '\\', '"' => '"', '/' => '/', ' ' => ' '
    ];

    $e= $in[$offset];
    if (isset($escapes[$e])) {
      $offset++;
      return $escapes[$e];
    } else if ('x' === $e && 1 === sscanf(substr($in, $offset + 1, 2), '%2x', $v)) {
      $offset+= 3;
      return chr($v);
    } else {
      throw new FormatException('Illegal escape sequence starting with \\'.$e);
    }
  }

  /**
   * Produces the next token
   *
   * @param  string $in
   * @param  int $offset
   * @param  string $end
   * @return var[]
   */
  private function token($in, &$offset, $end= '#') {
    $l= strlen($in);
    $offset+= strspn($in, ' ', $offset);
    if ($offset >= $l) return null;

    // fputs(STDERR, "T `$in`\n");
    // fputs(STDERR, "   ".str_repeat(' ', $offset)."^ ($offset)\n\n");

    $c= $in[$offset];
    if ('"' === $c) {
      $offset+= 1;
      $string= '';
      do {
        $p= strcspn($in, '\\"', $offset);
        $i= $offset + $p;
        $string.= substr($in, $offset, $p);
        if ($i >= $l) {
          $in.= $this->lines('double');
          $l= strlen($in);
        } else if ('\\' === $in[$i]) {
          $offset= $i + 1;
          $string.= $this->escape($in, $offset);
          continue;
        } else if ('"' === $in[$i]) {
          $offset+= $p + 1;
          return ['str', $string];
        }
        $offset+= $p;
      } while (true);
    } else if ("'" === $c) {
      $offset+= 1;
      $string= '';
      do {
        $p= strcspn($in, "'", $offset);
        $i= $offset + $p;
        $string.= substr($in, $offset, $p);
        if ($i >= $l) {
          $in.= $this->lines('single');
          $l= strlen($in);
        } else if ("'" === $in[$i]) {
          if ($i + 1 < $l && "'" === $in[$i + 1]) {
            $string.= "'";
            $p+= 2;
          } else {
            $offset+= $p + 1;
            return ['str', $string];
          }
        }
        $offset+= $p;
      } while (true);
    } else if ('#' === $c) {
      $offset= strlen($in);
      $in.= $this->nextLine();
      return $this->token($in, $offset, $end);
    } else if ('*' === $c) {
      $offset+= 1;
      $p= strcspn($in, $end, $offset);
      $literal= trim(substr($in, $offset, $p));
      $offset+= strlen($literal);
      return ['*', $literal];
    } else if ('[' === $c) {
      $offset++;
      $r= [];
      do {
        $value= $this->flow($in, $offset, 'sequence');
        if (']' === $value) break;
        $r[]= $value;
        $token= $this->flow($in, $offset, 'sequence');
      } while (',' === $token);
      return ['seq', $r];
    } else if ('{' === $c) {
      $offset++;
      $r= [];
      do {
        $key= $this->flow($in, $offset, 'map');
        if ('}' === $key) break;
        $token= $this->flow($in, $offset, 'map');
        if (':' !== $token) break;
        $r[$key[1]]= $this->flow($in, $offset, 'map');
        $token= $this->flow($in, $offset, 'map');
      } while (',' === $token);
      return ['map', $r];
    } else if ('>' === $c) {
      $mode= $offset + 1 < $l ? $in[$offset + 1] : self::CLIP;
      $offset= strlen($in);
      return ['str', $this->indented(' ', $mode)];
    } else if ('|' === $c) {
      $mode= $offset + 1 < $l ? $in[$offset + 1] : self::CLIP;
      $offset= strlen($in);
      return ['str', $this->indented("\n", $mode)];
    } else if (strspn($c, ':,]}') > 0) {
      $offset++;
      return $c;
    } else if (0 === substr_compare($in, '!!', $offset, 2)) {
      $p= strcspn($in, ' ', $offset);
      $tag= substr($in, $offset + 2, $p - 2);
      $offset+= $p + 1;
      if ($offset >= $l) return [$tag, null];
      $token= $this->token($in, $offset, $end);
      return [$tag, $token[1]];
    } else {
      $p= strcspn($in, $end, $offset);
      $literal= trim(substr($in, $offset, $p));
      $offset+= strlen($literal);

      if (0 === strncmp($literal, '0x', 2)) {
        return ['int', hexdec($literal)];
      } else if (0 === strncmp($literal, '0o', 2)) {
        return ['int', octdec($literal)];
      } else if (preg_match('/^[+-]?[0-9]+$/', $literal)) {
        return ['int', $literal];
      } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+(e\+[0-9]+)?$/', $literal)) {
        return ['float', $literal];
      } else if (preg_match('/^[0-9]{4}\-[0-9]{2}-[0-9]{2}+/', $literal)) {
        return ['date', $literal];
      } else {
        return array_key_exists($literal, self::$literals) ? ['literal', self::$literals[$literal]] : ['str', $literal];
      }
    }
  }
}