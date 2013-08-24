<?php namespace org\yaml;

class FileInput extends Input {
  protected $reader= null;

  /**
   * Creates a new reader input
   *
   * @param var arg either a File object or a string pointing to a file name
   */
  public function __construct($arg) {
    $this->reader= new \io\streams\TextReader(new \io\streams\FileInputStream($arg));
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