<?php namespace org\yaml\unittest;

use org\yaml\{StringInput, YamlParser};

/**
 * Abstract base class for YAML Parser tests
 */
abstract class AbstractYamlParserTest extends \unittest\TestCase {

  /**
   * Parse a given string and return the data
   *
   * @param  string $str
   * @param  [:var] $identifiers
   * @return [:var]
   * @throws lang.FormatException
   */
  protected function parse($str, $identifiers= []) {
    return (new YamlParser())->parse(new StringInput($str), $identifiers);
  }
}