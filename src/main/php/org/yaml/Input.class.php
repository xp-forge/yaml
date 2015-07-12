<?php namespace org\yaml;

abstract class Input extends \lang\Object {
  protected $stack= [];

  /**
   * Reads a line
   *
   * @return string or NULL to indicate EOF
   */
  protected abstract function readLine();

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
   * Gets tokens from input
   *
   * ```
   * "one" => "one"
   * "one, two" => "one", "two"
   * "[one, two]" => "[one, two]"
   * "[one, two], three" => "[one, two]", "three"
   * ```
   *
   * @param  string $in
   * @return string
   */
  public function nextToken(&$in) {
    $token= null;

    // fputs(STDERR, "\n> '$in'\n");
    do {

      // First, scan until we find non-space. If the next character we find
      // starts a sequence, return the entirety of that, same for maps as
      // well as single and double quoted strings. Next, scan until we find 
      // a comma (in sequences), or a colon (in maps).
      $p= strspn($in, ' ');
      if ($p >= strlen($in)) {
        $token= $in;
      } else if ('[' === $in{$p}) {
        $token= '['.$this->matching($in, '[', ']', $p).']';
      } else if ('{' === $in{$p}) {
        $token= '{'.$this->matching($in, '{', '}', $p).'}';
      } else if ('"' === $in{$p} ) {
        $token= '"'.$this->quoted($in, '"', '\\', $p).'"';
      } else if ("'" === $in{$p}) {
        $token= "'".$this->quoted($in, "'", "'", $p)."'";
      } else if (',' === $in{$p} || ':' === $in{$p}) {
        $in= substr($in, 1);
        continue;
      } else {
        $token= substr($in, 0, $p + strcspn($in, ',:', $p));
      }
    } while (null === $token);

    // fputs(STDERR, "  '$token'\n");
    $in= (string)substr($in, strlen($token) + 1);
    return trim($token);
  }

  /**
   * Returns characters between matching quote signs
   *
   * @param  string $value
   * @param  string $chr
   * @param  string $escape
   * @param  int $offset
   * @return string
   */
  public function quoted($value, $chr, $escape, $offset= 0) {
    for ($o= $offset+ strlen($chr), $i= $o, $l= strlen($value), $b= 1; $b > 0; $i++) {
      if ($i >= $l) {
        if (null === ($line= $this->nextLine())) {
          throw new \lang\FormatException('Unclosed '.$chr.' quote, encountered EOF');
        }
        $value.= $line;
        $l= strlen($value);
      }
      if ($escape === $value{$i} && $i + 1 < $l && $chr === $value{$i + 1}) {
        $i++;
      } else if ($chr === $value{$i}) {
        $b--;
      }
    }
    return substr($value, $o, $i - $o - strlen($chr));
  }

  /**
   * Returns characters between matching begin and end characters from 
   * a given reader. Continues to read lines until sequence is matched.
   *
   * @param  string $value
   * @param  string $begin
   * @param  string $end
   * @param  int $offset
   * @return string
   */
  public function matching($value, $begin, $end, $offset= 0) {
    for ($o= $offset+ strlen($begin), $i= $o, $b= 1; $b > 0; $i++) {
      if ($i >= strlen($value)) {
        if (null === ($line= $this->nextLine())) {
          throw new \lang\FormatException('Unmatched "'.$begin.'", encountered EOF');
        }
        $value.= $line;
      }
      if ($begin === $value{$i}) $b++; else if ($end === $value{$i}) $b--;
    }
    return substr($value, $o, $i - $o - strlen($end));
  }
}