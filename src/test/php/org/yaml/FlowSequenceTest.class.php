<?php namespace org\yaml;

/**
 * 7.4.1. Flow sequences
 * ---------------------
 * Flow sequence content is denoted by surrounding “[” and “]” characters.
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790320
 */
class FlowSequenceTest extends AbstractYamlParserTest {

  #[@test]
  public function with_surrounding_space() {
    $this->assertEquals(array('one', 'two'), $this->parse('[ one, two ]'));
  }

  #[@test]
  public function with_trailing_comma() {
    $this->assertEquals(array('one', 'two'), $this->parse('[ one, two, ]'));
  }

  #[@test]
  public function without_space() {
    $this->assertEquals(array('one', 'two'), $this->parse('[one,two]'));
  }

  #[@test]
  public function explicit() {
    $this->assertEquals(array('one', 'two'), $this->parse('!!seq [ one, two ]'));
  }

  #[@test]
  public function explicit_indented() {
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
}