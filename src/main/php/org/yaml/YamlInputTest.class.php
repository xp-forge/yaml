<?php namespace org\yaml;

use io\streams\TextReader;
use io\streams\MemoryInputStream;

class YamlInputTest extends AbstractYamlParserTest {

  /**
   * Creates a new fixture
   *
   * @param  string str
   * @return org.yaml.Input
   */
  protected function newFixture($str= '') {
    return new ReaderInput(new TextReader(new MemoryInputStream($str)));
  }

  #[@test]
  public function nextLine_for_empty_input() {
    $this->assertNull($this->newFixture('')->nextLine());
  }

  #[@test]
  public function nextLine_for_non_empty_input() {
    $this->assertEquals('Hello', $this->newFixture('Hello')->nextLine());
  }

  #[@test, @values(array("\r", "\n", "\r\n"))]
  public function nextLine_for_one_line_input($delimiter) {
    $this->assertEquals('Hello', $this->newFixture('Hello'.$delimiter)->nextLine());
  }

  #[@test, @values(array("\r", "\n", "\r\n"))]
  public function nextLine_for_two_lines_of_input($delimiter) {
    $fixture= $this->newFixture('Line 1'.$delimiter.'Line 2'.$delimiter);
    $this->assertEquals(
      array('Line 1', 'Line 2'),
      array($fixture->nextLine(), $fixture->nextLine())
    );
  }

  #[@test]
  public function resetLine() {
    $fixture= $this->newFixture('Hello');
    $fixture->resetLine($fixture->nextLine());
    $this->assertEquals('Hello', $fixture->nextLine());
  }

  protected function tokensOf($input) {
    $fixture= $this->newFixture();
    $tokens= array();
    while ($token= $fixture->nextToken($input)) {
      $tokens[]= $token;
    }
    return $tokens;
  }

  #[@test]
  public function single_token() {
    $this->assertEquals(array('hello'), $this->tokensOf('hello'));
  }

  #[@test]
  public function single_quoted_token() {
    $this->assertEquals(array('"hello"'), $this->tokensOf('"hello"'));
  }

  #[@test]
  public function comma_delimited_tokens() {
    $this->assertEquals(array('hello', 'world'), $this->tokensOf('hello, world'));
  }

  #[@test]
  public function comma_delimited_quoted_tokens() {
    $this->assertEquals(array('"hello"', '"world"'), $this->tokensOf('"hello", "world"'));
  }

  #[@test]
  public function colon_delimited_tokens() {
    $this->assertEquals(array('hello', 'world'), $this->tokensOf('hello: world'));
  }

  #[@test]
  public function single_element_seq_token() {
    $this->assertEquals(array('[hello]'), $this->tokensOf('[hello]'));
  }

  #[@test]
  public function seq_of_two_elements_token() {
    $this->assertEquals(array('[hello, world]'), $this->tokensOf('[hello, world]'));
  }

  #[@test]
  public function two_sequences() {
    $this->assertEquals(array('[a, b]', '[c, d]'), $this->tokensOf('[a, b], [c, d]'));
  }

  #[@test, @values(array('[a, b], [c, d], e', '[a, b] , [c, d] , e'))]
  public function two_sequences_and_single_token($value) {
    $this->assertEquals(array('[a, b]', '[c, d]', 'e'), $this->tokensOf($value));
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
}