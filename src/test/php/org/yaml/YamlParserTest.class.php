<?php namespace org\yaml;

class YamlParserTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new YamlParser();
  }

  protected function parse($str) {
    return create(new YamlParser())->parse(new ReaderInput(
      new \io\streams\TextReader(new \io\streams\MemoryInputStream($str))
    ));
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

  #[@test, @values(array(
  #  array('num: 1', 1), array('num: 0', 0),
  #  array('num: -1', -1), array('num: +1', 1),
  #  array('num: 0o14', 014), array('num: 0xC', 0xC)
  #))]
  public function parse_integer($input, $result) {
    $this->assertEquals(array('num' => $result), $this->parse($input));
  }

  #[@test, @values(array(
  #  array('num: 1.0', 1.0), array('num: 0.0', 0.0), array('num: 0.5', 0.5),
  #  array('num: -1.0', -1.0), array('num: +1.0', 1.0),
  #  array('num: 1.23015e+3', 1.23015e+3), array('num: 12.3015e+02', 12.3015e+02)
  #))]
  public function parse_float($input, $result) {
    $this->assertEquals(array('num' => $result), $this->parse($input));
  }

  #[@test, @values(array(array('bool: true', TRUE), array('bool: false', FALSE)))]
  public function parse_bool($input, $result) {
    $this->assertEquals(array('bool' => $result), $this->parse($input));
  }

  #[@test]
  public function parse_null() {
    $this->assertEquals(array('nil' => null), $this->parse('nil: '));
  }

  #[@test, @values(array(
  #  array("str: ''", ''), array("str: 'Test'", 'Test')
  #))]
  public function parse_string($input, $result) {
    $this->assertEquals(array('str' => $result), $this->parse($input));
  }

  #[@test]
  public function parse_sequence() {
    $this->assertEquals(
      array('Mark McGwire', 'Sammy Sosa', 'Ken Griffey'),
      $this->parse("- Mark McGwire\n- Sammy Sosa\n- Ken Griffey")
    );
  }

  #[@test]
  public function mapping_scalars_to_sequences() {
    $this->assertEquals(
      array(
        'american' => array('Boston Red Sox', 'Detroit Tigers', 'New York Yankees'),
        'national' => array('New York Mets', 'Chicago Cubs', 'Atlanta Braves')
      ),
      $this->parse(
        "american:\n  - Boston Red Sox\n  - Detroit Tigers\n  - New York Yankees\n".
        "national:\n  - New York Mets\n  - Chicago Cubs\n  - Atlanta Braves\n"
      )
    );
  }

  #[@test]
  public function sequence_of_mappings() {
    $this->assertEquals(
      array(
        array('name' => 'Mark McGwire', 'hr' => 65, 'avg' => 0.278),
        array('name' => 'Sammy Sosa', 'hr' => 63, 'avg' => 0.288)
      ),
      $this->parse(
        "-\n  name: Mark McGwire\n  hr:   65\n  avg:  0.278\n".
        "-\n  name: Sammy Sosa\n  hr:   63\n  avg:  0.288\n"
      )
    );
  }

  #[@test]
  public function parse_comment() {
    $this->assertEquals(array(), $this->parse('# Comments are ignored'));
  }

  #[@test]
  public function parse_comments() {
    $this->assertEquals(array(), $this->parse("# Line 1\n# Line 2\n"));
  }
}