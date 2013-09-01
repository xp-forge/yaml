<?php namespace org\yaml\unittest;

use org\yaml\StringInput;

/**
 * Tests the "String" input implementation
 */
class StringInputTest extends AbstractInputTest {

  /**
   * Creates a new fixture
   *
   * @param  string $str
   * @return org.yaml.Input
   */
  protected function newFixture($str= '') {
    return new StringInput($str);
  }
}