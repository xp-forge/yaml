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
  #  array('str: ""', ''), array('str: "Test"', 'Test'),
  #  array('str: "\\""', '"'), array('str: "\\"Test\\""', '"Test"')
  #))]
  public function double_quotes($input, $result) {
    $this->assertEquals(array('str' => $result), $this->parse($input));
  }

  #[@test, @values(array(
  #  array('\0', "\x00"), array('\a', "\x07"), array('\b', "\x08"), array('\t', "\x09"),
  #  array('\n', "\x0a"), array('\v', "\x0b"), array('\f', "\x0c"), array('\r', "\x0d"),
  #  array('\e', "\x1b")
  #))]
  public function control_chars_inside_double_quotes($input, $result) {
    $this->assertEquals(
      array('str' => '<'.$result.'>'),
      $this->parse('str: "<'.$input.'>"')
    );
  }

  #[@test, @values(array('\c', '\xq-')), @expect('lang.FormatException')]
  public function invalid_escape_characters($input) {
    $this->parse('str: "<'.$input.'>"');
  }

  #[@test]
  public function backslash_inside_double_quotes() {
    $this->assertEquals(
      array('str' => '\\'),
      $this->parse('str: "\\\\"')   // The input string is "\\"
    );
  }

  #[@test, @values(array('\ ', ' '))]
  public function space_may_be_escaped_inside_double_quotes_to_force_spaces($variant) {
    $this->assertEquals(array('str' => ' '), $this->parse('str: "'.$variant.'"'));
  }

  #[@test, @values(array('\/', '/'))]
  public function slash_may_be_escaped_inside_double_quotes_for_json_compat($variant) {
    $this->assertEquals(array('str' => '/'), $this->parse('str: "'.$variant.'"'));
  }

  #[@test]
  public function hex_escapes_inside_double_quotes() {
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