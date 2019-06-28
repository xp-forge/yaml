<?php namespace org\yaml\unittest;

use lang\FormatException;

/**
 * 7.4. Flow Collection Styles
 * ===========================
 *
 * 7.4.1. Flow sequences
 * ---------------------
 * Flow sequence content is denoted by surrounding “[” and “]” characters.
 *
 * 7.4.2. Flow Mappings
 * ---------------------
 * Flow mappings are denoted by surrounding “{” and “}” characters.
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790320
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790832
 */
class FlowCollectionsTest extends AbstractYamlParserTest {

  #[@test]
  public function seq_with_surrounding_space() {
    $this->assertEquals(['one', 'two'], $this->parse('[ one, two ]'));
  }

  #[@test]
  public function seq_with_trailing_comma() {
    $this->assertEquals(['one', 'two'], $this->parse('[ one, two, ]'));
  }

  #[@test]
  public function seq_without_space() {
    $this->assertEquals(['one', 'two'], $this->parse('[one,two]'));
  }

  #[@test]
  public function explicit_seq() {
    $this->assertEquals(['one', 'two'], $this->parse('!!seq [ one, two ]'));
  }

  #[@test]
  public function explicit_seq_indented() {
    $this->assertEquals(['one', 'two'], $this->parse("!!seq [\n  one,\n  two\n]"));
  }

  #[@test]
  public function sequence_of_sequences() {
    $this->assertEquals(
      [
        ['name', 'hr', 'avg'],
        ['Mark McGwire', 65, 0.278],
        ['Sammy Sosa', 63, 0.288]
      ],
      $this->parse(
        "- [name        , hr, avg  ]\n".
        "- [Mark McGwire, 65, 0.278]\n".
        "- [Sammy Sosa  , 63, 0.288]\n"
      )
    );
  }

  #[@test]
  public function nested_sequence() {
    $this->assertEquals(
      [['one', 'two']],
      $this->parse('[[one, two]]')
    );
  }

  #[@test]
  public function nested_sequences() {
    $this->assertEquals(
      [['one', 'two'], ['three', 'four']],
      $this->parse('[[one, two], [three, four]]')
    );
  }

  #[@test]
  public function map_with_surrounding_space() {
    $this->assertEquals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function map_with_trailing_comma() {
    $this->assertEquals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function map_without_space() {
    $this->assertEquals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function explicit_map() {
    $this->assertEquals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('!!map { one : two , three: four }')
    );
  }

  #[@test]
  public function flowstyle_mappings_of_mappings() {
    $this->assertEquals(
      [
        'Mark McGwire' => ['hr' => 65, 'avg' => 0.278],
        'Sammy Sosa'   => ['hr' => 63, 'avg' => 0.288]
      ],
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
  public function nested_map() {
    $this->assertEquals(
      ['map' => ['one' => 'two', 'three' => 'four']],
      $this->parse('{ map: { one : two , three: four } }')
    );
  }

  #[@test]
  public function nested_maps() {
    $this->assertEquals(
      ['a' => ['one' => 1, 'two' => 2], 'b' => ['three' => 3, 'four' => 4]],
      $this->parse('{ a: { one : 1 , two: 2 }, b: { three: 3, four: 4 } }')
    );
  }

  #[@test, @expect(FormatException::class)]
  public function malformed_seq() {
    $this->parse('!!seq {}');
  }

  #[@test, @expect(FormatException::class)]
  public function malformed_map() {
    $this->parse('!!map []');
  }
}