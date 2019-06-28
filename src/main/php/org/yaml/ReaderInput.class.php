<?php namespace org\yaml;

use io\streams\TextReader;

class ReaderInput extends Input {
  protected $reader= null;

  /**
   * Creates a new reader input
   *
   * @param io.streams.TextReader $reader
   */
  public function __construct(TextReader $reader) {
    $this->reader= $reader;
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
    $this->reader->atBeginning() || $this->reader->reset();
  }
}