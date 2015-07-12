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

  #[@test, @values([
  #  ['str: ""', ''], ['str: "Test"', 'Test'],
  #  ['str: "\\""', '"'], ['str: "\\"Test\\""', '"Test"']
  #])]
  public function double_quotes($input, $result) {
    $this->assertEquals(['str' => $result], $this->parse($input));
  }

  #[@test, @values([
  #  ['\0', "\x00"], ['\a', "\x07"], ['\b', "\x08"], ['\t', "\x09"],
  #  ['\n', "\x0a"], ['\v', "\x0b"], ['\f', "\x0c"], ['\r', "\x0d"],
  #  ['\e', "\x1b"]
  #])]
  public function control_chars_inside_double_quotes($input, $result) {
    $this->assertEquals(
      ['str' => '<'.$result.'>'],
      $this->parse('str: "<'.$input.'>"')
    );
  }

  #[@test, @values(['\c', '\xq-']), @expect('lang.FormatException')]
  public function invalid_escape_characters($input) {
    $this->parse('str: "<'.$input.'>"');
  }

  #[@test]
  public function backslash_inside_double_quotes() {
    $this->assertEquals(
      ['str' => '\\'],
      $this->parse('str: "\\\\"')   // The input string is "\\"
    );
  }

  #[@test, @values(['\ ', ' '])]
  public function space_may_be_escaped_inside_double_quotes_to_force_spaces($variant) {
    $this->assertEquals(['str' => ' '], $this->parse('str: "'.$variant.'"'));
  }

  #[@test, @values(['\/', '/'])]
  public function slash_may_be_escaped_inside_double_quotes_for_json_compat($variant) {
    $this->assertEquals(['str' => '/'], $this->parse('str: "'.$variant.'"'));
  }

  #[@test]
  public function hex_escapes_inside_double_quotes() {
    $this->assertEquals(
      ['str' => "\x0d\x0a is \x0d\x0a"],
      $this->parse('str: "\x0d\x0a is \r\n"')
    );
  }

  #[@test, @values([["str: ''", ''], ["str: 'Test'", 'Test']])]
  public function single_quotes($input, $result) {
    $this->assertEquals(['str' => $result], $this->parse($input));
  }
}