<?php namespace org\yaml;

class StringInput extends Input {
  protected $reader= null;

  /**
   * Creates a new string input
   *
   * @param string str
   */
  public function __construct($str) {
    $this->reader= new \io\streams\TextReader(new \io\streams\MemoryInputStream($str));
  }

  /**
   * Reads a line
   *
   * @return string or NULL to indicate EOF
   */
  protected function readLine() {
    return $this->reader->readLine();
  }
}