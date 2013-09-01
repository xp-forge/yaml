<?php namespace org\yaml;

abstract class Input extends \lang\Object {
  protected $stack= array();

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
      } else if ('"' === $in{$p} || "'" === $in{$p}) {
        $token= $in{$p}.$this->ending($in, $in{$p}, $p).$in{$p};
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

  public function ending($value, $chr, $offset= 0) {
    for ($o= $offset+ strlen($chr), $i= $o, $b= 1; $b > 0; $i++) {
      if ($i >= strlen($value)) {
        if (null === ($line= $this->nextLine())) {
          throw new \lang\FormatException('Unmatched "'.$chr.'", encountered EOF');
        }
        $value.= $line;
      }
      if ($chr === $value{$i}) $b--;
    }
    return substr($value, $o, $i - $o - strlen($chr));
  }

  /**
   * Returns characters between matching begin and end characters from 
   * a given reader. Continues to read lines until sequence is matched.
   *
   * ```
   * matching("[hello]", "[", "]") = "hello"
   * matching("[[hello],[world]]", "[", "]") = "[hello],[world]"
   * ```
   *
   * @param  string $value
   * @param  string $begin
   * @param  string $end
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