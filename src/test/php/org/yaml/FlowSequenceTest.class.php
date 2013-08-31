<?php namespace org\yaml;

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