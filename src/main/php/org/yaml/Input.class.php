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
}