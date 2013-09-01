<?php namespace org\yaml\unittest;

use org\yaml\FileInput;
use io\streams\MemoryInputStream;

/**
 * Tests the "File" input implementation
 */
class FileInputTest extends AbstractInputTest {

  /**
   * Creates a new fixture
   *
   * @param  string $str
   * @return org.yaml.Input
   */
  protected function newFixture($str= '') {
    return new FileInput(newinstance('io.File', array($str), '{
      public function __construct($str) { $this->str= $str; $this->pos= 0; }
      public function open($mode= "rb") { return true; }
      public function exists() { return true; }
      public function size() { return strlen($this->str); }
      public function tell() { return $this->pos; }
      public function seek($pos= 0, $mode= 0) { $this->pos= $pos; }
      public function read($bytes= 4096) {
        $chunk= substr($this->str, $this->pos, $bytes);
        $this->pos+= strlen($chunk);
        return $chunk;
      }
    }'));
  }
}