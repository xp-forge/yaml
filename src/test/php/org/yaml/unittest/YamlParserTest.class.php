<?php namespace org\yaml\unittest;

use lang\IllegalArgumentException;
use org\yaml\YamlParser;
use util\Bytes;
use util\Date;

class YamlParserTest extends AbstractYamlParserTest {

  #[@test]
  public function can_create() {
    new YamlParser();
  }

  #[@test]
  public function parse_empty() {
    $this->assertNull($this->parse(''));
  }

  #[@test, @values(["\n", "\n\n", " \n \n", "  \n\n"])]
  public function parse_lines($value) {
    $this->assertNull($this->parse($value));
  }

  #[@test, @values(["key: value", "key: value\n"])]
  public function parse_single_key_value($value) {
    $this->assertEquals(['key' => 'value'], $this->parse($value));
  }

  #[@test]
  public function parse_single_key_value_surrounded_by_empty_lines() {
    $this->assertEquals(['key' => 'value'], $this->parse("\nkey: value\n\n"));
  }

  #[@test]
  public function parse_yaml_directive() {
    $this->assertNull($this->parse('%YAML 1.2'));
  }

  #[@test]
  public function parse_yaml_directive_separated_from_content() {
    $this->assertEquals(['key' => 'value'], $this->parse("%YAML 1.2\n---\nkey: value"));
  }

  #[@test]
  public function parse_key_value() {
    $this->assertEquals(
      ['time' => '20:03:20', 'player' => 'Sammy Sosa', 'action' => 'strike (miss)'],
      $this->parse("time: 20:03:20\nplayer: Sammy Sosa\naction: strike (miss)")
    );
  }

  #[@test, @values([
  #  '',
  #  "\n", "\n\n",
  #  "\n  ", "\n  \n    ",
  #  "# Comment", "# Comment\n# Another comment\n",
  #  "# Comment\n\n",
  #])]
  public function issue_2($between) {
    $this->assertEquals(
      ['context' => ['text' => ['Test' => 'Probieren'], 'user' => ['language' => 'de']]],
      $this->parse("context:\n  text:\n    Test: Probieren".$between."\n  user:\n    language: de\n")
    );
  }

  #[@test, @values([
  #  ['str: Test', "Test"],
  #  ['str: Test # Comment', "Test"],
  #  ['str: "Test"', "Test"],
  #  ['str: "A:B"', "A:B"],
  #  ['str: "A\'B"', "A'B"],
  #  ["str: 'A\"B'", 'A"B'],
  #  ['str: "Test # No comment"', "Test # No comment"],
  #  ['str: "Test # No comment" ', "Test # No comment"],
  #  ['str: "Test # No comment" # Comment', "Test # No comment"],
  #  ['str: "He said: \"Hello\""', 'He said: "Hello"'],
  #  ["str: 'He said: ''Hello'''", "He said: 'Hello'"],
  #])]
  public function parse_string($input, $result) {
    $this->assertEquals(['str' => $result], $this->parse($input));
  }

  #[@test, @values([
  #  ['num: 1', 1], ['num: 0', 0],
  #  ['num: -1', -1], ['num: +1', 1],
  #  ['num: 0o14', 12], ['num: 0xC', 12], ['num: 0xc', 12]
  #])]
  public function parse_integer($input, $result) {
    $this->assertEquals(['num' => $result], $this->parse($input));
  }

  #[@test, @values([
  #  ['num: 1.0', 1.0], ['num: 0.0', 0.0], ['num: 0.5', 0.5],
  #  ['num: -1.0', -1.0], ['num: +1.0', 1.0],
  #  ['num: 1.23015e+3', 1.23015e+3], ['num: 12.3015e+02', 12.3015e+02]
  #])]
  public function parse_float($input, $result) {
    $this->assertEquals(['num' => $result], $this->parse($input));
  }

  #[@test, @values(['nan: .nan', 'nan: .NaN', 'nan: .NAN'])]
  public function parse_nan($input) {
    $r= $this->parse($input);
    $this->assertTrue(is_nan($r['nan']), $r['nan']);
  }

  #[@test, @values([
  #  ['num: .inf', INF], ['num: .Inf', INF], ['num: .INF', INF],
  #  ['num: -.inf', -INF], ['num: -.Inf', -INF], ['num: -.INF', -INF],
  #  ['num: +.inf', +INF], ['num: +.Inf', +INF], ['num: +.INF', +INF],
  #])]
  public function parse_inf($input, $result) {
    $this->assertEquals(['num' => $result], $this->parse($input));
  }

  #[@test, @values([
  #  ['bool: true', true], ['bool: True', true], ['bool: TRUE', true],
  #  ['bool: false', false], ['bool: False', false], ['bool: FALSE', false]
  #])]
  public function parse_bool($input, $result) {
    $this->assertEquals(['bool' => $result], $this->parse($input));
  }

  #[@test, @values(['nil: ', 'nil: null', 'nil: Null', 'nil: NULL', 'nil: ~'])]
  public function parse_null($value) {
    $this->assertEquals(['nil' => null], $this->parse($value));
  }

  #[@test]
  public function parse_date() {
    $this->assertEquals(['date' => new Date('2002-12-14')], $this->parse('date: 2002-12-14'));
  }

  #[@test]
  public function parse_canonical() {
    $this->assertEquals(
      ['canonical' => new Date('2001-12-15 02:59:43', \util\TimeZone::getByName('GMT'))],
      $this->parse('canonical: 2001-12-15T02:59:43.1Z')
    );
  }

  #[@test]
  public function parse_iso8601() {
    $this->assertEquals(
      ['iso8601' => new Date('2001-12-14 21:59:43-05:00')],
      $this->parse('iso8601: 2001-12-14t21:59:43.10-05:00')
    );
  }

  #[@test]
  public function spaced() {
    $this->assertEquals(
      ['spaced' => new Date('2001-12-14 21:59:43-05:00')],
      $this->parse('spaced: 2001-12-14 21:59:43.10 -5')
    );
  }

  #[@test]
  public function parse_sequence() {
    $this->assertEquals(
      ['Mark McGwire', 'Sammy Sosa', 'Ken Griffey'],
      $this->parse("- Mark McGwire\n- Sammy Sosa\n- Ken Griffey")
    );
  }

  #[@test]
  public function mapping_scalars_to_sequences() {
    $this->assertEquals(
      [
        'american' => ['Boston Red Sox', 'Detroit Tigers', 'New York Yankees'],
        'national' => ['New York Mets', 'Chicago Cubs', 'Atlanta Braves']
      ],
      $this->parse(
        "american:\n  - Boston Red Sox\n  - Detroit Tigers\n  - New York Yankees\n".
        "national:\n  - New York Mets\n  - Chicago Cubs\n  - Atlanta Braves\n"
      )
    );
  }

  #[@test]
  public function sequence_of_mappings() {
    $this->assertEquals(
      [
        ['name' => 'Mark McGwire', 'hr' => 65, 'avg' => 0.278],
        ['name' => 'Sammy Sosa', 'hr' => 63, 'avg' => 0.288]
      ],
      $this->parse(
        "-\n  name: Mark McGwire\n  hr:   65\n  avg:  0.278\n".
        "-\n  name: Sammy Sosa\n  hr:   63\n  avg:  0.288\n"
      )
    );
  }

  #[@test]
  public function comment() {
    $this->assertNull($this->parse('# Comments are ignored'));
  }

  #[@test]
  public function indented_comment() {
    $this->assertNull($this->parse('  # Comments are ignored'));
  }

  #[@test]
  public function comments() {
    $this->assertNull($this->parse("# Line 1\n# Line 2\n"));
  }

  #[@test]
  public function comments_and_whitespace() {
    $this->assertNull($this->parse("# Line 1\n\n# Line 3\n"));
  }

  #[@test, @values(['key: value # A value', 'key: value        # A value'])]
  public function comment_at_end_of_line($value) {
    $this->assertEquals(['key' => 'value'], $this->parse($value));
  }

  #[@test]
  public function comment_at_end_of_lines() {
    $this->assertEquals(
      ['hr' => 65, 'avg' => 0.278, 'rbi' => 147],
      $this->parse("hr:  65    # Home runs\navg: 0.278 # Batting average\nrbi: 147   # Runs Batted In")
    );
  }

  #[@test]
  public function compact_nested_mapping() {
    $this->assertEquals(
      [
        ['item' => 'Super Hoop', 'quantity' => 1],
        ['item' => 'Basketball', 'quantity' => 4],
        ['item' => 'Big Shoes', 'quantity' => 1]
      ],
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
      ['stats' => "65 Home Runs\n0.278 Batting Average\n"],
      $this->parse("stats: |
        65 Home Runs
        0.278 Batting Average
      ")
    );
  }

  #[@test]
  public function folded_style_clipped() {
    $this->assertEquals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury.\n"],
      $this->parse("sentence: >
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[@test]
  public function folded_style_stripped() {
    $this->assertEquals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury."],
      $this->parse("sentence: >-
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[@test]
  public function folded_style_keeping() {
    $this->assertEquals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury.\n\n"],
      $this->parse("sentence: >+
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[@test]
  public function folded_scalars() {
    $this->assertEquals(
      ['one' => "This is sentence number 1\n", 'two' => "This is sentence number 2\n"],
      $this->parse("one: >\n  This is sentence\n  number 1\ntwo: >\n  This is sentence\n  number 2\n")
    );
  }

  #[@test]
  public function explicit_str_tag() {
    $this->assertEquals(['not-date' => '2002-04-28'], $this->parse('not-date: !!str 2002-04-28'));
  }

  #[@test]
  public function binary_tag() {
    $this->assertEquals(['key' => new Bytes('YAML')], $this->parse('key: !!binary "WUFNTA=="'));
  }

  #[@test]
  public function binary_tag_with_literal() {
    $gif= (
      "GIF89a\f\000\f\000\204\000\000\377\377\367\365\365\356".
      "\351\351\345fff\000\000\000\347\347\347^^^\363\363\355".
      "\216\216\216\340\340\340\237\237\237\223\223\223\247\247".
      "\247\236\236\236i^\020' \202\n\001\000;"
    );
    $this->assertEquals(['key' => new Bytes($gif)], $this->parse('key: !!binary |
      R0lGODlhDAAMAIQAAP//9/X
      17unp5WZmZgAAAOfn515eXv
      Pz7Y6OjuDg4J+fn5OTk6enp
      56enmleECcgggoBADs=
    '));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function unknown_explicit_tag() {
    $this->parse('!!test X');
  }

  #[@test, @values([
  #  ['!!int 3', 3], ['!!int 0', 0], ['!!int -1', -1],
  #  ['!!int 0o7', 7], ['!!int 0x3A', 58]
  #])]
  public function explicit_int_tag($input, $value) {
    $this->assertEquals(['r' => $value], $this->parse('r: '.$input));
  }

  #[@test, @values([
  #  ['!!float 0.3', 0.3], ['!!float 0.0', 0.0], ['!!float -1.0', -1.0],
  #  ['!!float 3', 3.0], ['!!float 0.', 0.0], ['!!float .5', 0.5],
  #  ['!!float +.INF', INF], ['!!float -.INF', -INF]
  #])]
  public function explicit_float_tag($input, $value) {
    $this->assertEquals(['r' => $value], $this->parse('r: '.$input));
  }

  #[@test]
  public function explicit_float_nan() {
    $r= $this->parse('nan: !!float .NAN');
    $this->assertTrue(is_nan($r['nan']), $r['nan']);
  }

  #[@test, @values([
  #  ['!!bool true', true], ['!!bool TRUE', true], ['!!bool True', true],
  #  ['!!bool false', false], ['!!bool FALSE', false], ['!!bool False', false]
  #])]
  public function explicit_bool_tag($input, $value) {
    $this->assertEquals(['r' => $value], $this->parse('r: '.$input));
  }

  #[@test, @values(['!!null ""', '!!null'])]
  public function explicit_null_tag($input) {
    $this->assertEquals(['r' => null], $this->parse('r: '.$input));
  }
}