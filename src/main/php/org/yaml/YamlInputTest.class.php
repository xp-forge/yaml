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