<?php namespace org\yaml\unittest;

use io\streams\MemoryInputStream;
use io\streams\TextReader;
use lang\FormatException;

/**
 * Abstract base class for YAML Input tests
 */
abstract class AbstractInputTest extends AbstractYamlParserTest {

  /**
   * Creates a new fixture
   *
   * @param  string str
   * @return org.yaml.Input
   */
  protected abstract function newFixture($str= '');

  #[@test]
  public function nextLine_for_empty_input() {
    $this->assertNull($this->newFixture('')->nextLine());
  }

  #[@test]
  public function nextLine_for_non_empty_input() {
    $this->assertEquals('Hello', $this->newFixture('Hello')->nextLine());
  }

  #[@test, @values(["\r", "\n", "\r\n"])]
  public function nextLine_for_one_line_input($delimiter) {
    $this->assertEquals('Hello', $this->newFixture('Hello'.$delimiter)->nextLine());
  }

  #[@test, @values(["\r", "\n", "\r\n"])]
  public function nextLine_for_two_lines_of_input($delimiter) {
    $fixture= $this->newFixture('Line 1'.$delimiter.'Line 2'.$delimiter);
    $this->assertEquals(
      ['Line 1', 'Line 2'],
      [$fixture->nextLine(), $fixture->nextLine()]
    );
  }

  #[@test]
  public function resetLine() {
    $fixture= $this->newFixture('Hello');
    $fixture->resetLine($fixture->nextLine());
    $this->assertEquals('Hello', $fixture->nextLine());
  }

  /**
   * Helper which gathers all tokens from `nextToken()` in an array
   *
   * @param  string $input
   * @return string[]
   */
  protected function tokensOf($input) {
    $fixture= $this->newFixture();
    $tokens= [];
    while ($token= $fixture->nextToken($input)) {
      $tokens[]= $token;
    }
    return $tokens;
  }

  #[@test, @values(['hello', '"hello"', "'hello'", '"He said: \"Hello\""', "'He said: ''Hello'''"])]
  public function single_token($value) {
    $this->assertEquals([$value], $this->tokensOf($value));
  }

  #[@test, @values(['a, b', 'a ,b', 'a , b'])]
  public function comma_delimited_tokens($value) {
    $this->assertEquals(['a', 'b'], $this->tokensOf($value));
  }

  #[@test, @values(['"a", "b"', "'a', 'b'"])]
  public function comma_delimited_quoted_tokens($value) {
    $this->assertEquals(explode(', ', $value), $this->tokensOf($value));
  }

  #[@test, @values(['a: b', 'a : b', 'a   :   b'])]
  public function colon_delimited_tokens($value) {
    $this->assertEquals(['a', 'b'], $this->tokensOf($value));
  }

  #[@test, @values(['"a": "b"', "'a': 'b'"])]
  public function colon_delimited_quoted_tokens($value) {
    $this->assertEquals(explode(': ', $value), $this->tokensOf($value));
  }

  #[@test, @values(['[a]', '["a"]', "['a']", '[a, b]'])]
  public function sequence_token($value) {
    $this->assertEquals([$value], $this->tokensOf($value));
  }

  #[@test, @values(['{a: b}', '{"a": "b"}', "{'a': 'c'}", '{a: b, c: d}'])]
  public function mapping_token($value) {
    $this->assertEquals([$value], $this->tokensOf($value));
  }

  #[@test, @values(['[a, b], [c, d]', '[a, b] , [c, d]'])]
  public function two_sequences($value) {
    $this->assertEquals(['[a, b]', '[c, d]'], $this->tokensOf($value));
  }

  #[@test, @values(['[a, b], [c, d], e', '[a, b] , [c, d] , e'])]
  public function two_sequences_and_single_token($value) {
    $this->assertEquals(['[a, b]', '[c, d]', 'e'], $this->tokensOf($value));
  }

  #[@test, @values(['{a, b}, {c, d}', '{a, b} , {c, d}'])]
  public function two_maps($value) {
    $this->assertEquals(['{a, b}', '{c, d}'], $this->tokensOf($value));
  }

  #[@test, @values(['{a, b}, {c, d}, e', '{a, b} , {c, d} , e'])]
  public function two_maps_and_single_token($value) {
    $this->assertEquals(['{a, b}', '{c, d}', 'e'], $this->tokensOf($value));
  }

  #[
  #  @test,
  #  @values(['"hello', "'hello", '"hello \"', "'hello ''"]),
  #  @expect(class= FormatException::class, withMessage= '/Unclosed . quote, encountered EOF/')
  #]
  public function unclosed_quote($value) {
    $this->tokensOf($value);
  }

  #[
  #  @test,
  #  @values(['[one', '[one, []', '[one, [nested]', '[', '[[[']),
  #  @expect(class= FormatException::class, withMessage= '/Unmatched "\[", encountered EOF/')
  #]
  public function unclosed_sequence($value) {
    $this->tokensOf($value);
  }

  #[
  #  @test,
  #  @values(['{one: two', '{one: two, {}', '{', '{{{']),
  #  @expect(class= FormatException::class, withMessage= '/Unmatched "\{", encountered EOF/')
  #]
  public function unclosed_map($value) {
    $this->tokensOf($value);
  }

  #[@test]
  public function matching() {
    $this->assertEquals('hello', $this->newFixture()->matching('[hello]', '[', ']'));
  }

  #[@test]
  public function matching_nested() {
    $this->assertEquals('[hello]', $this->newFixture()->matching('[[hello]]', '[', ']'));
  }

  #[@test]
  public function matching_nested2() {
    $this->assertEquals('[hello],[world]', $this->newFixture()->matching('[[hello],[world]]', '[', ']'));
  }

  #[@test]
  public function matching_nested_at_offset0() {
    $this->assertEquals('[hello],[world]', $this->newFixture()->matching('[[hello],[world]]', '[', ']', 0));
  }

  #[@test]
  public function matching_nested_at_offset1() {
    $this->assertEquals('hello', $this->newFixture()->matching('[[hello],[world]]', '[', ']', 1));
  }

  #[@test]
  public function matching_nested_at_offset9() {
    $this->assertEquals('world', $this->newFixture()->matching('[[hello],[world]]', '[', ']', 9));
  }

  #[@test]
  public function utf8_is_default() {
    $this->assertEquals('Über', $this->newFixture('Über')->nextLine());
  }
}