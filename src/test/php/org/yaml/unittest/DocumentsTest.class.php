<?php namespace org\yaml\unittest;

use org\yaml\StringInput;
use org\yaml\YamlParser;
use unittest\TestCase;

class DocumentsTest extends TestCase {

  private function documents($str) {
    return (new YamlParser())->documents(new StringInput($str));
  }

  #[@test]
  public function empty_input() {
    $this->assertEquals([], iterator_to_array($this->documents('')));
  }

  #[@test]
  public function empty_document() {
    $this->assertEquals([[]], iterator_to_array($this->documents(
      "---\n".
      "...\n"
    )));
  }

  #[@test]
  public function single_document() {
    $this->assertEquals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n".
      "...\n"
    )));
  }

  #[@test]
  public function single_document_ended_by_eof() {
    $this->assertEquals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n"
    )));
  }

  #[@test]
  public function multiple_documents() {
    $this->assertEquals([['A', 'B'], ['C', 'D']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n".
      "---\n".
      "- C\n".
      "- D\n".
      "...\n"
    )));
  }

  #[@test]
  public function directives() {
    $this->assertEquals([['A', 'B'], ['C', 'D']], iterator_to_array($this->documents(
      "%YAML 1.2\n".
      "---\n".
      "- A\n".
      "- B\n".
      "...\n".
      "%YAML 1.2\n".
      "---\n".
      "- C\n".
      "- D\n".
      "...\n"
    )));
  }
}