<?php namespace org\yaml\unittest;

use io\File;
use org\yaml\FileInput;
use io\streams\Streams;
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
    return new FileInput(new File(Streams::readableFd(new MemoryInputStream($str))));
  }
}