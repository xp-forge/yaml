<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\MemoryInputStream;

abstract class AbstractYamlParserTest extends \unittest\TestCase {

  protected function parse($str) {
    return create(new YamlParser())->parse(new ReaderInput(new TextReader(new MemoryInputStream($str))));
  }
}