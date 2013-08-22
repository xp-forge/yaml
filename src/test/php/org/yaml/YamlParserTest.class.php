<?php namespace org\yaml;

class YamlParserTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new YamlParser();
  }

  protected function parse($str) {
    return create(new YamlParser())->parse(new \io\streams\TextReader(new \io\streams\MemoryInputStream($str)));
  }

  #[@test]
  public function parse_empty() {
    $this->assertEquals(array(), $this->parse(''));
  }

  #[@test]
  public function parse_single_key_value() {
    $this->assertEquals(array('key' => 'value'), $this->parse('key: value'));
  }

  #[@test]
  public function parse_key_value() {
    $this->assertEquals(
      array('time' => '20:03:20', 'player' => 'Sammy Sosa', 'action' => 'strike (miss)'),
      $this->parse("time: 20:03:20\nplayer: Sammy Sosa\naction: strike (miss)")
    );
  }
}