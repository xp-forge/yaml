<?php namespace org\yaml\unittest;

use lang\FormatException;
use test\{Assert, Expect, Test, Values};

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

  #[Test, Values([['str: ""', ''], ['str: "Test"', 'Test'], ['str: "\\""', '"'], ['str: "\\"Test\\""', '"Test"'], ['str: "A \\" B"', 'A " B'],])]
  public function double_quotes($input, $result) {
    Assert::equals(['str' => $result], $this->parse($input));
  }

  #[Test, Values([['\0', "\x00"], ['\a', "\x07"], ['\b', "\x08"], ['\t', "\x09"], ['\n', "\x0a"], ['\v', "\x0b"], ['\f', "\x0c"], ['\r', "\x0d"], ['\e', "\x1b"]])]
  public function control_chars_inside_double_quotes($input, $result) {
    Assert::equals(
      ['str' => '<'.$result.'>'],
      $this->parse('str: "<'.$input.'>"')
    );
  }

  #[Test, Values(['\c', '\xq-']), Expect(FormatException::class)]
  public function invalid_escape_characters($input) {
    $this->parse('str: "<'.$input.'>"');
  }

  #[Test]
  public function backslash_inside_double_quotes() {
    Assert::equals(
      ['str' => '\\'],
      $this->parse('str: "\\\\"')   // The input string is "\\"
    );
  }

  #[Test, Values(['\ ', ' '])]
  public function space_may_be_escaped_inside_double_quotes_to_force_spaces($variant) {
    Assert::equals(['str' => ' '], $this->parse('str: "'.$variant.'"'));
  }

  #[Test, Values(['\/', '/'])]
  public function slash_may_be_escaped_inside_double_quotes_for_json_compat($variant) {
    Assert::equals(['str' => '/'], $this->parse('str: "'.$variant.'"'));
  }

  #[Test]
  public function hex_escapes_inside_double_quotes() {
    Assert::equals(
      ['str' => "\x0d\x0a is \x0d\x0a"],
      $this->parse('str: "\x0d\x0a is \r\n"')
    );
  }

  #[Test, Values([["str: ''", ''], ["str: 'Test'", 'Test'], ["str: 'A '' B'", 'A \' B']])]
  public function single_quotes($input, $result) {
    Assert::equals(['str' => $result], $this->parse($input));
  }

  #[Test, Values(["str: 'A string\nspanning\nmultiple\nlines\n\nNew line\n  .\n\n\nEnd'", "str: \"A string\nspanning\nmultiple\nlines\n\nNew line\n  .\n\n\nEnd\"",])]
  public function multiline_string($declaration) {
    Assert::equals(
      ['str' => "A string spanning multiple lines\nNew line .\n\nEnd"],
      $this->parse($declaration)
    );
  }
}