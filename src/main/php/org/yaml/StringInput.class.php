<?php namespace org\yaml;

use io\streams\MemoryInputStream;
use io\streams\TextReader;

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

  /**
   * Rewind this input to the beginning
   *
   * @return void
   */
  public function rewind() {
    $this->reader->reset();
  }
}