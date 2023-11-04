<?php namespace org\yaml\unittest;

use lang\IllegalArgumentException;
use org\yaml\YamlParser;
use test\{Assert, Expect, Test, Values};
use util\{Bytes, Date};

class YamlParserTest extends AbstractYamlParserTest {

  #[Test]
  public function can_create() {
    new YamlParser();
  }

  #[Test]
  public function parse_empty() {
    Assert::null($this->parse(''));
  }

  #[Test, Values(["\n", "\n\n", " \n \n", "  \n\n"])]
  public function parse_lines($value) {
    Assert::null($this->parse($value));
  }

  #[Test, Values(["key: value", "key: value\n"])]
  public function parse_single_key_value($value) {
    Assert::equals(['key' => 'value'], $this->parse($value));
  }

  #[Test]
  public function parse_single_key_value_surrounded_by_empty_lines() {
    Assert::equals(['key' => 'value'], $this->parse("\nkey: value\n\n"));
  }

  #[Test]
  public function parse_yaml_directive() {
    Assert::null($this->parse('%YAML 1.2'));
  }

  #[Test]
  public function parse_yaml_directive_separated_from_content() {
    Assert::equals(['key' => 'value'], $this->parse("%YAML 1.2\n---\nkey: value"));
  }

  #[Test]
  public function parse_key_value() {
    Assert::equals(
      ['time' => '20:03:20', 'player' => 'Sammy Sosa', 'action' => 'strike (miss)'],
      $this->parse("time: 20:03:20\nplayer: Sammy Sosa\naction: strike (miss)")
    );
  }

  #[Test, Values(['', "\n", "\n\n", "\n  ", "\n  \n    ", "# Comment", "# Comment\n# Another comment\n", "# Comment\n\n",])]
  public function issue_2($between) {
    Assert::equals(
      ['context' => ['text' => ['Test' => 'Probieren'], 'user' => ['language' => 'de']]],
      $this->parse("context:\n  text:\n    Test: Probieren".$between."\n  user:\n    language: de\n")
    );
  }

  #[Test, Values([['str: Test', "Test"], ['str: Test # Comment', "Test"], ['str: "Test"', "Test"], ['str: "A:B"', "A:B"], ['str: "A\'B"', "A'B"], ["str: 'A\"B'", 'A"B'], ['str: "Test # No comment"', "Test # No comment"], ['str: "Test # No comment" ', "Test # No comment"], ['str: "Test # No comment" # Comment', "Test # No comment"], ['str: "He said: \"Hello\""', 'He said: "Hello"'], ["str: 'He said: ''Hello'''", "He said: 'Hello'"],])]
  public function parse_string($input, $result) {
    Assert::equals(['str' => $result], $this->parse($input));
  }

  #[Test, Values([['num: 1', 1], ['num: 0', 0], ['num: -1', -1], ['num: +1', 1], ['num: 0o14', 12], ['num: 0xC', 12], ['num: 0xc', 12]])]
  public function parse_integer($input, $result) {
    Assert::equals(['num' => $result], $this->parse($input));
  }

  #[Test, Values([['num: 1.0', 1.0], ['num: 0.0', 0.0], ['num: 0.5', 0.5], ['num: -1.0', -1.0], ['num: +1.0', 1.0], ['num: 1.23015e+3', 1.23015e+3], ['num: 12.3015e+02', 12.3015e+02]])]
  public function parse_float($input, $result) {
    Assert::equals(['num' => $result], $this->parse($input));
  }

  #[Test, Values(['nan: .nan', 'nan: .NaN', 'nan: .NAN'])]
  public function parse_nan($input) {
    $r= $this->parse($input);
    Assert::true(is_nan($r['nan']), $r['nan']);
  }

  #[Test, Values([['num: .inf', INF], ['num: .Inf', INF], ['num: .INF', INF], ['num: -.inf', -INF], ['num: -.Inf', -INF], ['num: -.INF', -INF], ['num: +.inf', +INF], ['num: +.Inf', +INF], ['num: +.INF', +INF],])]
  public function parse_inf($input, $result) {
    Assert::equals(['num' => $result], $this->parse($input));
  }

  #[Test, Values([['bool: true', true], ['bool: True', true], ['bool: TRUE', true], ['bool: false', false], ['bool: False', false], ['bool: FALSE', false]])]
  public function parse_bool($input, $result) {
    Assert::equals(['bool' => $result], $this->parse($input));
  }

  #[Test, Values(['nil: ', 'nil: null', 'nil: Null', 'nil: NULL', 'nil: ~'])]
  public function parse_null($value) {
    Assert::equals(['nil' => null], $this->parse($value));
  }

  #[Test]
  public function parse_date() {
    Assert::equals(['date' => new Date('2002-12-14')], $this->parse('date: 2002-12-14'));
  }

  #[Test]
  public function parse_canonical() {
    Assert::equals(
      ['canonical' => new Date('2001-12-15 02:59:43', \util\TimeZone::getByName('GMT'))],
      $this->parse('canonical: 2001-12-15T02:59:43.1Z')
    );
  }

  #[Test]
  public function parse_iso8601() {
    Assert::equals(
      ['iso8601' => new Date('2001-12-14 21:59:43-05:00')],
      $this->parse('iso8601: 2001-12-14t21:59:43.10-05:00')
    );
  }

  #[Test]
  public function spaced() {
    Assert::equals(
      ['spaced' => new Date('2001-12-14 21:59:43-05:00')],
      $this->parse('spaced: 2001-12-14 21:59:43.10 -5')
    );
  }

  #[Test]
  public function parse_sequence() {
    Assert::equals(
      ['Mark McGwire', 'Sammy Sosa', 'Ken Griffey'],
      $this->parse("- Mark McGwire\n- Sammy Sosa\n- Ken Griffey")
    );
  }

  #[Test, Values([['key', 'key'], ['"key"', 'key'], ["'key'", 'key'], ['key b', 'key b'], ['"key:"', 'key:'], ['"key::b"', 'key::b'], ['"key \"b\""', 'key "b"'], ["'key ''b'''", "key 'b'"]])]
  public function string_keys($declaration, $expected) {
    Assert::equals([$expected => 1], $this->parse("{$declaration}: 1"));
  }

  #[Test]
  public function mapping_scalars_to_sequences() {
    Assert::equals(
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

  #[Test]
  public function sequence_of_mappings() {
    Assert::equals(
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

  #[Test]
  public function comment() {
    Assert::null($this->parse('# Comments are ignored'));
  }

  #[Test]
  public function indented_comment() {
    Assert::null($this->parse('  # Comments are ignored'));
  }

  #[Test]
  public function comments() {
    Assert::null($this->parse("# Line 1\n# Line 2\n"));
  }

  #[Test]
  public function comments_and_whitespace() {
    Assert::null($this->parse("# Line 1\n\n# Line 3\n"));
  }

  #[Test, Values(['key: value # A value', 'key: value        # A value'])]
  public function comment_at_end_of_line($value) {
    Assert::equals(['key' => 'value'], $this->parse($value));
  }

  #[Test]
  public function comment_at_end_of_lines() {
    Assert::equals(
      ['hr' => 65, 'avg' => 0.278, 'rbi' => 147],
      $this->parse("hr:  65    # Home runs\navg: 0.278 # Batting average\nrbi: 147   # Runs Batted In")
    );
  }

  #[Test]
  public function compact_nested_mapping() {
    Assert::equals(
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

  #[Test]
  public function literal_style() {
    Assert::equals(
      ['stats' => "65 Home Runs\n0.278 Batting Average\n"],
      $this->parse("stats: |
        65 Home Runs
        0.278 Batting Average
      ")
    );
  }

  #[Test]
  public function folded_style_clipped() {
    Assert::equals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury.\n"],
      $this->parse("sentence: >
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[Test]
  public function folded_style_stripped() {
    Assert::equals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury."],
      $this->parse("sentence: >-
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[Test]
  public function folded_style_keeping() {
    Assert::equals(
      ['sentence' => "Mark McGwire's year was crippled by a knee injury.\n\n"],
      $this->parse("sentence: >+
        Mark McGwire's
        year was crippled
        by a knee injury.

      ")
    );
  }

  #[Test]
  public function folded_scalars() {
    Assert::equals(
      ['one' => "This is sentence number 1\n", 'two' => "This is sentence number 2\n"],
      $this->parse("one: >\n  This is sentence\n  number 1\ntwo: >\n  This is sentence\n  number 2\n")
    );
  }

  #[Test]
  public function explicit_str_tag() {
    Assert::equals(['not-date' => '2002-04-28'], $this->parse('not-date: !!str 2002-04-28'));
  }

  #[Test]
  public function binary_tag() {
    Assert::equals(['key' => new Bytes('YAML')], $this->parse('key: !!binary "WUFNTA=="'));
  }

  #[Test]
  public function binary_tag_with_literal() {
    $gif= (
      "GIF89a\f\000\f\000\204\000\000\377\377\367\365\365\356".
      "\351\351\345fff\000\000\000\347\347\347^^^\363\363\355".
      "\216\216\216\340\340\340\237\237\237\223\223\223\247\247".
      "\247\236\236\236i^\020' \202\n\001\000;"
    );
    Assert::equals(['key' => new Bytes($gif)], $this->parse('key: !!binary |
      R0lGODlhDAAMAIQAAP//9/X
      17unp5WZmZgAAAOfn515eXv
      Pz7Y6OjuDg4J+fn5OTk6enp
      56enmleECcgggoBADs=
    '));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function unknown_explicit_tag() {
    $this->parse('!!test X');
  }

  #[Test, Values([['!!int 3', 3], ['!!int 0', 0], ['!!int -1', -1], ['!!int 0o7', 7], ['!!int 0x3A', 58]])]
  public function explicit_int_tag($input, $value) {
    Assert::equals(['r' => $value], $this->parse('r: '.$input));
  }

  #[Test, Values([['!!float 0.3', 0.3], ['!!float 0.0', 0.0], ['!!float -1.0', -1.0], ['!!float 3', 3.0], ['!!float 0.', 0.0], ['!!float .5', 0.5], ['!!float +.INF', INF], ['!!float -.INF', -INF]])]
  public function explicit_float_tag($input, $value) {
    Assert::equals(['r' => $value], $this->parse('r: '.$input));
  }

  #[Test]
  public function explicit_float_nan() {
    $r= $this->parse('nan: !!float .NAN');
    Assert::true(is_nan($r['nan']), $r['nan']);
  }

  #[Test, Values([['!!bool true', true], ['!!bool TRUE', true], ['!!bool True', true], ['!!bool false', false], ['!!bool FALSE', false], ['!!bool False', false]])]
  public function explicit_bool_tag($input, $value) {
    Assert::equals(['r' => $value], $this->parse('r: '.$input));
  }

  #[Test, Values(['!!null ""', '!!null'])]
  public function explicit_null_tag($input) {
    Assert::equals(['r' => null], $this->parse('r: '.$input));
  }
}