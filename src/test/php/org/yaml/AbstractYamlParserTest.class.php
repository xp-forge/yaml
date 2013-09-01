<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\MemoryInputStream;

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
    return create(new YamlParser())->parse(new ReaderInput(new TextReader(new MemoryInputStream($str))));
  }
}