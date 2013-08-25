<?php namespace org\yaml;

use util\Date;
use io\streams\TextReader;
use io\streams\MemoryInputStream;

class YamlParserTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new YamlParser();
  }

  protected function parse($str) {
    return create(new YamlParser())->parse(new ReaderInput(new TextReader(new MemoryInputStream($str))));
  }

  #[@test]
  public function parse_empty() {
    $this->assertEquals(array(), $this->parse(''));
  }

  #[@test, @values(array("\n", "\n\n", " \n \n", "  \n\n"))]
  public function parse_lines($value) {
    $this->assertEquals(array(), $this->parse($value));
  }

  #[@test, @values(array("key: value", "key: value\n"))]
  public function parse_single_key_value($value) {
    $this->assertEquals(array('key' => 'value'), $this->parse($value));
  }

  #[@test]
  public function parse_single_key_value_surrounded_by_empty_lines() {
    $this->assertEquals(array('key' => 'value'), $this->parse("\nkey: value\n\n"));
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

  #[@test, @values(array('nil: ', 'nil: null', 'nil: Null', 'nil: NULL', 'nil: ~'))]
  public function parse_null($value) {
    $this->assertEquals(array('nil' => null), $this->parse($value));
  }

  #[@test]
  public function parse_date() {
    $this->assertEquals(array('date' => new Date('2002-12-14')), $this->parse('date: 2002-12-14'));
  }

  #[@test]
  public function parse_canonical() {
    $this->assertEquals(
      array('canonical' => new Date('2001-12-15 02:59:43', \util\TimeZone::getByName('GMT'))),
      $this->parse('canonical: 2001-12-15T02:59:43.1Z')
    );
  }

  #[@test]
  public function parse_iso8601() {
    $this->assertEquals(
      array('iso8601' => new Date('2001-12-14 21:59:43-05:00')),
      $this->parse('iso8601: 2001-12-14t21:59:43.10-05:00')
    );
  }

  #[@test]
  public function spaced() {
    $this->assertEquals(
      array('spaced' => new Date('2001-12-14 21:59:43-05:00')),
      $this->parse('spaced: 2001-12-14 21:59:43.10 -5')
    );
  }

  #[@test, @values(array(
  #  array("str: ''", ''), array("str: 'Test'", 'Test'),
  #  array('str: ""', ''), array('str: "Test"', 'Test')
  #))]
  public function parse_string($input, $result) {
    $this->assertEquals(array('str' => $result), $this->parse($input));
  }

  #[@test]
  public function control() {
    $this->assertEquals(
      array('str' => "\x081998\x091999\x092000\x0a"),
      $this->parse('str: "\b1998\t1999\t2000\n"')
    );
  }

  #[@test]
  public function hex_esc() {
    $this->assertEquals(
      array('str' => "\x0d\x0a is \x0d\x0a"),
      $this->parse('str: "\x0d\x0a is \r\n"')
    );
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
  public function flowstyle_mappings_of_mappings() {
    $this->assertEquals(
      array(
        'Mark McGwire' => array('hr' => 65, 'avg' => 0.278),
        'Sammy Sosa'   => array('hr' => 63, 'avg' => 0.288)
      ),
      $this->parse(
        "Mark McGwire: {hr: 65, avg: 0.278}\n".
        "Sammy Sosa: {\n".
        "    hr: 63,\n".
        "    avg: 0.288\n".
        "  }"
      )
    );
  }

  #[@test]
  public function flowstyle_sequence_of_sequences() {
    $this->assertEquals(
      array(
        array('name', 'hr', 'avg'),
        array('Mark McGwire', 65, 0.278),
        array('Sammy Sosa', 63, 0.288)
      ),
      $this->parse(
        "- [name        , hr, avg  ]\n".
        "- [Mark McGwire, 65, 0.278]\n".
        "- [Sammy Sosa  , 63, 0.288]\n"
      )
    );
  }

  #[@test]
  public function comment() {
    $this->assertEquals(array(), $this->parse('# Comments are ignored'));
  }

  #[@test]
  public function indented_comment() {
    $this->assertEquals(array(), $this->parse('  # Comments are ignored'));
  }

  #[@test]
  public function comments() {
    $this->assertEquals(array(), $this->parse("# Line 1\n# Line 2\n"));
  }

  #[@test]
  public function comments_and_whitespace() {
    $this->assertEquals(array(), $this->parse("# Line 1\n\n# Line 3\n"));
  }

  #[@test, @values(array('key: value # A value', 'key: value        # A value'))]
  public function comment_at_end_of_line($value) {
    $this->assertEquals(array('key' => 'value'), $this->parse($value));
  }

  #[@test]
  public function comment_at_end_of_lines() {
    $this->assertEquals(
      array('hr' => 65, 'avg' => 0.278, 'rbi' => 147),
      $this->parse("hr:  65    # Home runs\navg: 0.278 # Batting average\nrbi: 147   # Runs Batted In")
    );
  }

  #[@test]
  public function repeated_nodes() {
    $this->assertEquals(
      array('Sammy Sosa', 'Sammy Sosa'),
      $this->parse("- &SS Sammy Sosa\n- *SS # Same\n")
    );
  }

  #[@test]
  public function compact_nested_mapping() {
    $this->assertEquals(
      array(
        array('item' => 'Super Hoop', 'quantity' => 1),
        array('item' => 'Basketball', 'quantity' => 4),
        array('item' => 'Big Shoes', 'quantity' => 1)
      ),
      $this->parse(
        "# Products purchased\n".
        "- item    : Super Hoop\n".
        "  quantity: 1\n".
        "- item    : Basketball\n".
        "  quantity: 4\n".
        "- item    : Big Shoes\n".
        "  quantity: 1"
      )
    );
  }

  #[@test]
  public function literal_style() {
    $this->assertEquals(
      array('stats' => "65 Home Runs\n0.278 Batting Average"),
      $this->parse("stats: |\n 65 Home Runs\n 0.278 Batting Average")
    );
  }

  #[@test]
  public function folded_scalar() {
    $this->assertEquals(
      array('sentence' => "Mark McGwire's year was crippled by a knee injury."),
      $this->parse("sentence: >\n  Mark McGwire's\n  year was crippled\n  by a knee injury.")
    );
  }

  #[@test]
  public function folded_scalars() {
    $this->assertEquals(
      array('one' => 'This is sentence number 1', 'two' => 'This is sentence number 2'),
      $this->parse("one: >\n  This is sentence\n  number 1\ntwo: >\n  This is sentence\n  number 2\n")
    );
  }

  #[@test]
  public function explicit_str_tag() {
    $this->assertEquals(array('not-date' => '2002-04-28'), $this->parse('not-date: !!str 2002-04-28'));
  }
}