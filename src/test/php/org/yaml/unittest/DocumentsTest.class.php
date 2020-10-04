<?php namespace org\yaml\unittest;

use org\yaml\{StringInput, YamlParser};
use unittest\{Test, TestCase};

class DocumentsTest extends TestCase {

  private function documents($str) {
    return (new YamlParser())->documents(new StringInput($str));
  }

  #[Test]
  public function empty_input() {
    $this->assertEquals([], iterator_to_array($this->documents('')));
  }

  #[Test]
  public function empty_document() {
    $this->assertEquals([null], iterator_to_array($this->documents(
      "---\n".
      "...\n"
    )));
  }

  #[Test]
  public function single_document() {
    $this->assertEquals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n".
      "...\n"
    )));
  }

  #[Test]
  public function single_document_ended_by_eof() {
    $this->assertEquals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n"
    )));
  }

  #[Test]
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

  #[Test]
  public function empty_document_between() {
    $this->assertEquals([['A', 'B'], null, ['C', 'D']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n".
      "---\n".
      "...\n".
      "---\n".
      "- C\n".
      "- D\n".
      "...\n"
    )));
  }

  #[Test]
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