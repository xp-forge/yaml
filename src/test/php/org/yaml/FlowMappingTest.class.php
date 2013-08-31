<?php namespace org\yaml;

/**
 * 7.4.2. Flow Mappings
 * ---------------------
 * Flow mappings are denoted by surrounding “{” and “}” characters.
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790832
 */
class FlowMappingTest extends AbstractYamlParserTest {

  #[@test]
  public function with_surrounding_space() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function with_trailing_comma() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function without_space() {
    $this->assertEquals(
      array('one' => 'two', 'three' => 'four'),
      $this->parse('{ one : two , three: four }')
    );
  }

  #[@test]
  public function explicit() {
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
}