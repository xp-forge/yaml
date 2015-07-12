<?php namespace org\yaml\unittest;

use org\yaml\YamlParser;
use org\yaml\StringInput;

/**
 * Abstract base class for YAML Parser tests
 */
abstract class AbstractYamlParserTest extends \unittest\TestCase {

  /**
   * Parse a given string and return the data
   *
   * @param  string $str
   * @return [:var]
   * @throws lang.FormatException
   */
  protected function parse($str) {
    return (new YamlParser())->parse(new StringInput($str));
  }
}