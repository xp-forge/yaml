<?php namespace org\yaml\unittest;

use io\File;
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
    return new FileInput(newinstance(File::class, [$str], [
      '__construct' => function($str) { $this->str= $str; $this->pos= 0; },
      'open' => function($mode= "rb") { return true; },
      'exists' => function() { return true; },
      'size' => function() { return strlen($this->str); },
      'tell' => function() { return $this->pos; },
      'seek' => function($pos= 0, $mode= 0) { $this->pos= $pos; },
      'read' => function($bytes= 4096) {
        $chunk= substr($this->str, $this->pos, $bytes);
        $this->pos+= strlen($chunk);
        return $chunk;
      }
    ]));
  }
}