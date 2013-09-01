<?php namespace org\yaml\unittest;

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
    return create(new \org\yaml\YamlParser())->parse(new \org\yaml\StringInput($str));
  }
}