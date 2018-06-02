<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\MemoryInputStream;

class StringInput extends Input {
  protected $reader= null;

  /**
   * Creates a new string input
   *
   * @param  string $str
   * @param  string $charset Defaults to UTF-8
   */
  public function __construct($str, $charset= 'utf-8') {
    $this->reader= new TextReader(new MemoryInputStream($str), $charset);
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