<?php namespace org\yaml\unittest;

use org\yaml\ReaderInput;
use io\streams\TextReader;
use io\streams\MemoryInputStream;

/**
 * Tests the "Reader" input implementation
 */
class ReaderInputTest extends AbstractInputTest {

  /**
   * Creates a new fixture
   *
   * @param  string $str
   * @return org.yaml.Input
   */
  protected function newFixture($str= '') {
    return new ReaderInput(new TextReader(new MemoryInputStream($str), 'utf-8'));
  }
}