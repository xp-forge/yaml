<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\FileInputStream;

class FileInput extends Input {
  protected $reader= null;

  /**
   * Creates a new reader input
   *
   * @param io.File|string $in object or a string pointing to a file name
   */
  public function __construct($in) {
    $this->reader= new TextReader(new FileInputStream($in));
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