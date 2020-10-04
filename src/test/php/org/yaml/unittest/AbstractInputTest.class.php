<?php namespace org\yaml\unittest;

use io\streams\{MemoryInputStream, TextReader};
use lang\FormatException;
use unittest\{Test, Values};

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

  #[Test]
  public function nextLine_for_empty_input() {
    $this->assertNull($this->newFixture('')->nextLine());
  }

  #[Test]
  public function nextLine_for_non_empty_input() {
    $this->assertEquals('Hello', $this->newFixture('Hello')->nextLine());
  }

  #[Test, Values(["\r", "\n", "\r\n"])]
  public function nextLine_for_one_line_input($delimiter) {
    $this->assertEquals('Hello', $this->newFixture('Hello'.$delimiter)->nextLine());
  }

  #[Test, Values(["\r", "\n", "\r\n"])]
  public function nextLine_for_two_lines_of_input($delimiter) {
    $fixture= $this->newFixture('Line 1'.$delimiter.'Line 2'.$delimiter);
    $this->assertEquals(
      ['Line 1', 'Line 2'],
      [$fixture->nextLine(), $fixture->nextLine()]
    );
  }

  #[Test]
  public function resetLine() {
    $fixture= $this->newFixture('Hello');
    $fixture->resetLine($fixture->nextLine());
    $this->assertEquals('Hello', $fixture->nextLine());
  }

  #[Test, Values([['', null], [' ', null], ['        ', null], ['Hello', ['str', 'Hello']], ['Hello ', ['str', 'Hello']], ['""', ['str', '']], ['"" ', ['str', '']], ['"\""', ['str', '"']], ['"Hello"', ['str', 'Hello']], ["'Hello'", ['str', 'Hello']], ['"Hello #"', ['str', 'Hello #']], ["'Hello #'", ['str', 'Hello #']], ['42', ['int', '42']], ['0o755', ['int', 493]], ['0xbeef', ['int', 48879]], ['6.1', ['float', '6.1']], ['true', ['literal', true]], ['True', ['literal', true]], ['false', ['literal', false]], ['False', ['literal', false]], ['null', ['literal', null]], ['Null', ['literal', null]], ['~', ['literal', null]], ['!!int 5', ['int', '5']], ['[]', ['seq', []]], ['[1]', ['seq', [['int', '1']]]], ['[1, 2, 3]', ['seq', [['int', '1'], ['int', '2'], ['int', '3']]]], ['{}', ['map', []]], ['{one: two}', ['map', ['one' => ['str', 'two']]]], ['{one: two, three: four}', ['map', ['one' => ['str', 'two'], 'three' => ['str', 'four']]]],])]
  public function token($input, $expected) {
    $this->assertEquals($expected, $this->newFixture()->tokenIn($input));
  }

  #[Test, Values(['"hello', "'hello", '"hello \"', "'hello ''"]), Expect(class: FormatException::class, withMessage: '/Unclosed .+ quote, encountered EOF/')]
  public function unclosed_quote($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[Test, Values(['[one', '[one, []', '[one, [nested]', '[', '[[[']), Expect(class: FormatException::class, withMessage: '/Encountered EOF while parsing sequence/')]
  public function unclosed_sequence($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[Test, Values(['{one: two', '{one: two, {}', '{', '{{{']), Expect(class: FormatException::class, withMessage: '/Encountered EOF while parsing map/')]
  public function unclosed_map($value) {
    $this->newFixture()->tokenIn($value);
  }

  #[Test]
  public function utf8_is_default() {
    $this->assertEquals('Über', $this->newFixture('Über')->nextLine());
  }

  #[Test]
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