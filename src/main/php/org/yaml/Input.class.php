<?php namespace org\yaml;

abstract class Input extends \lang\Object {
  protected $stack= array();

  /**
   * Reads a line
   *
   * @return string or NULL to indicate EOF
   */
  protected abstract function readLine();

  public function resetLine($l) {
    $this->stack[]= $l;
  }

  public function nextLine() {
    if ($this->stack) {
      return array_shift($this->stack);
    }
    return $this->readLine();
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
  public function matching($value, $begin, $end) {
    for ($o= strpos($value, $begin)+ strlen($begin), $i= $o, $b= 1; $b > 0; $i++) {
      if ($i >= strlen($value)) {
        if (null === ($line= $this->nextLine())) {
          throw new \lang\FormatException('Unmatched "'.$begin.'", encountered EOF');
        }
        $value.= $line;
      }
      if ($begin === $value{$i}) $b++; else if ($end === $value{$i}) $b--;
    }
    return substr($value, $o, $i - $o - 1);
  }
}