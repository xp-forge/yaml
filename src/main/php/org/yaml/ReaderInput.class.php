<?php namespace org\yaml;

class ReaderInput extends Input {
  protected $reader= null;

  /**
   * Creates a new reader input
   *
   * @param io.streams.TextReader $reader
   */
  public function __construct(\io\streams\TextReader $reader) {
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
}