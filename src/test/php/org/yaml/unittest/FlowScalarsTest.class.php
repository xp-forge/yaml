<?php namespace org\yaml\unittest;

/**
 * 7.3. Flow Scalar Styles
 * =======================
 * YAML provides three flow scalar styles: double-quoted, single-
 * quoted and plain (unquoted). Each provides a different trade-off
 * between readability and expressive power.
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2786942
 */
class FlowScalarsTest extends AbstractYamlParserTest {

  #[@test, @values(array(
  #  array('str: ""', ''), array('str: "Test"', 'Test')
  # ))]
  public function double_quotes($input, $result) {
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

  #[@test, @values(array(
  #  array("str: ''", ''), array("str: 'Test'", 'Test'),
  #))]
  public function single_quotes($input, $result) {
    $this->assertEquals(array('str' => $result), $this->parse($input));
  }
}