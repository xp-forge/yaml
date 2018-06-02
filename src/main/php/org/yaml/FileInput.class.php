<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\FileInputStream;

class FileInput extends Input {
  protected $reader= null;

  /**
   * Creates a new reader input
   *
   * @param io.File|string $in object or a string pointing to a file name
   * @param  string $charset Defaults to UTF-8
   */
  public function __construct($in, $charset= 'utf-8') {
    $this->reader= new TextReader(new FileInputStream($in), $charset);
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