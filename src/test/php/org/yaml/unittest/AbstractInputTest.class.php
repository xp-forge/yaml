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
   * @param  string $str
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

  #[@test, @values([
  #  ['', null],
  #  [' ', null],
  #  ['        ', null],
  #  ['Hello', ['str', 'Hello']],
  #  ['Hello ', ['str', 'Hello']],
  #  ['""', ['str', '']],
  #  ['"" ', ['str', '']],
  #  ['"\""', ['str', '"']],
  #  ['"Hello"', ['str', 'Hello']],
  #  ["'Hello'", ['str', 'Hello']],
  #  ['"Hello #"', ['str', 'Hello #']],
  #  ["'Hello #'", ['str', 'Hello #']],
  #  ['42', ['int', '42']],
  #  ['0o755', ['int', 0755]],
  #  ['0xbeef', ['int', 0xbeef]],
  #  ['6.1', ['float', '6.1']],
  #  ['true', ['literal', true]],
  #  ['True', ['literal', true]],
  #  ['false', ['literal', false]],
  #  ['False', ['literal', false]],
  #  ['null', ['literal', null]],
  #  ['Null', ['literal', null]],
  #  ['~', ['literal', null]],
  #  ['!!int 5', ['int', '5']],
  #  ['[]', ['seq', []]],
  #  ['[1]', ['seq', [['int', '1']]]],
  #  ['[1, 2, 3]', ['seq', [['int', '1'], ['int', '2'], ['int', '3']]]],
  #  ['{}', ['map', []]],
  #  ['{one: two}', ['map', ['one' => ['str', 'two']]]],
  #  ['{one: two, three: four}', ['map', ['one' => ['str', 'two'], 'three' => ['str', 'four']]]],
  #])]
  public function token($input, $expected) {
    $this->assertEquals($expected, $this->newFixture()->tokenIn($input));
  }

  #[
  #  @test,
  #  @values(['"hello', "'hello", '"hello \"', "'hello ''"]),
  #  @expect(class= FormatException::class, withMessage= '/Unclosed .+ quote, encountered EOF/')
  #]
  public function unclosed_quote($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[
  #  @test,
  #  @values(['[one', '[one, []', '[one, [nested]', '[', '[[[']),
  #  @expect(class= FormatException::class, withMessage= '/Encountered EOF while parsing sequence/')
  #]
  public function unclosed_sequence($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[
  #  @test,
  #  @values(['{one: two', '{one: two, {}', '{', '{{{']),
  #  @expect(class= FormatException::class, withMessage= '/Encountered EOF while parsing map/')
  #]
  public function unclosed_map($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[@test]
  public function utf8_is_default() {
    $this->assertEquals('Über', $this->newFixture('Über')->nextLine());
  }

  #[@test]
  public function rewind() {
    $lines= [];
    $r= $this->newFixture("Line 1\nLine 2");

    $lines[]= $r->nextLine();
    $r->rewind();
    $lines[]= $r->nextLine();
    $lines[]= $r->nextLine();

    $this->assertEquals(['Line 1', 'Line 1', 'Line 2'], $lines);
  }
}