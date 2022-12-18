<?php namespace org\yaml\unittest;

use io\streams\{MemoryInputStream, TextReader};
use lang\FormatException;
use unittest\{Assert, Test, Values};

/** Abstract base class for YAML Input tests */
abstract class AbstractInputTest extends AbstractYamlParserTest {

  /**
   * Creates a new fixture
   *
   * @param  string $str
   * @return org.yaml.Input
   */
  protected abstract function newFixture($str= '');

  /** @return iterable */
  private function tokens() {
    yield ['', null];
    yield [' ', null];
    yield ['        ', null];
    yield ['Hello', ['str', 'Hello']];
    yield ['Hello ', ['str', 'Hello']];
    yield ['""', ['str', '']];
    yield ['"" ', ['str', '']];
    yield ['"\""', ['str', '"']];
    yield ['"Hello"', ['str', 'Hello']];
    yield ["'Hello'", ['str', 'Hello']];
    yield ['"Hello #"', ['str', 'Hello #']];
    yield ["'Hello #'", ['str', 'Hello #']];
    yield ['42', ['int', '42']];
    yield ['0o755', ['int', 493]];
    yield ['0xbeef', ['int', 48879]];
    yield ['6.1', ['float', '6.1']];
    yield ['true', ['literal', true]];
    yield ['True', ['literal', true]];
    yield ['false', ['literal', false]];
    yield ['False', ['literal', false]];
    yield ['null', ['literal', null]];
    yield ['Null', ['literal', null]];
    yield ['~', ['literal', null]];
    yield ['!!int 5', ['int', '5']];
    yield ['[]', ['seq', []]];
    yield ['[1]', ['seq', [['int', '1']]]];
    yield ['[1, 2, 3]', ['seq', [['int', '1'], ['int', '2'], ['int', '3']]]];
    yield ['{}', ['map', []]];
    yield ['{one: two}', ['map', ['one' => ['str', 'two']]]];
    yield ['{one: two, three: four}', ['map', ['one' => ['str', 'two'], 'three' => ['str', 'four']]]];
  }

  #[Test]
  public function nextLine_for_empty_input() {
    Assert::null($this->newFixture('')->nextLine());
  }

  #[Test]
  public function nextLine_for_non_empty_input() {
    Assert::equals('Hello', $this->newFixture('Hello')->nextLine());
  }

  #[Test, Values(["\r", "\n", "\r\n"])]
  public function nextLine_for_one_line_input($delimiter) {
    Assert::equals('Hello', $this->newFixture('Hello'.$delimiter)->nextLine());
  }

  #[Test, Values(["\r", "\n", "\r\n"])]
  public function nextLine_for_two_lines_of_input($delimiter) {
    $fixture= $this->newFixture('Line 1'.$delimiter.'Line 2'.$delimiter);
    Assert::equals(
      ['Line 1', 'Line 2'],
      [$fixture->nextLine(), $fixture->nextLine()]
    );
  }

  #[Test]
  public function resetLine() {
    $fixture= $this->newFixture('Hello');
    $fixture->resetLine($fixture->nextLine());
    Assert::equals('Hello', $fixture->nextLine());
  }

  #[Test, Values('tokens')]
  public function token($input, $expected) {
    Assert::equals($expected, $this->newFixture()->tokenIn($input));
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
    Assert::equals('Über', $this->newFixture('Über')->nextLine());
  }

  #[Test]
  public function rewind() {
    $lines= [];
    $r= $this->newFixture("Line 1\nLine 2");

    $lines[]= $r->nextLine();
    $r->rewind();
    $lines[]= $r->nextLine();
    $lines[]= $r->nextLine();

    Assert::equals(['Line 1', 'Line 1', 'Line 2'], $lines);
  }
}