<?php namespace org\yaml;

class YamlParserTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new YamlParser();
  }
}