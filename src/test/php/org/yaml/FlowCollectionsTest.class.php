<?php namespace org\yaml;

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
    $this->assertEquals(array('one', 'two'), $this->parse('[ one, two ]'));
  }

  #[@test]
  public function seq_with_trailing_comma() {
    $this->assertEquals(array('one', 'two'), $this->parse('[ one, two, ]'));
  }

  #[@test]
  public function seq_without_space() {
    $this->assertEquals(array('one', 'two'), $this->parse('[one,two]'));
  }

  #[@test]
  public function explicit_seq() {
    $this->assertEquals(array('one', 'two'), $this->parse('!!seq [ one, two ]'));
  }

  #[@test]
  public function explicit_seq_indented() {
    $this->assertEquals(array('one', 'two'), $this->parse("!!seq [\n  one,\n  two\n]"));
  }

  #[@test]
  public function sequence_of_sequences() {
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
  public function nested_sequence() {
    $this->assertEquals(
      array(array('one', 'two')),
      $this->parse('[[one, two]]')
    );
  }

  #[@test]
  public function nested_sequences() {
    $this->assertEquals(
      array(array('one', 'two'), array('three', 'four')),
      $this->parse('[[one, two], [three, four]]')
    );
  }

  #[@test]
  public function map_with_surrounding_space() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function map_with_trailing_comma() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function map_without_space() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function explicit_map() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('!!map { one : two , three: four }')
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
  public function nested_map() {
    $this->assertEquals(
      array('map' => array('one' => 'two', 'three' => 'four')),
      $this->parse('{ map: { one : two , three: four } }')
    );
  }
}