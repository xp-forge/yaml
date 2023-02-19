<?php namespace org\yaml\unittest;

use org\yaml\{StringInput, YamlParser};
use test\{Assert, Test};

class DocumentsTest {

  private function documents($str) {
    return (new YamlParser())->documents(new StringInput($str));
  }

  #[Test]
  public function empty_input() {
    Assert::equals([], iterator_to_array($this->documents('')));
  }

  #[Test]
  public function empty_document() {
    Assert::equals([null], iterator_to_array($this->documents(
      "---\n".
      "...\n"
    )));
  }

  #[Test]
  public function single_document() {
    Assert::equals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n".
      "...\n"
    )));
  }

  #[Test]
  public function single_document_ended_by_eof() {
    Assert::equals([['A', 'B']], iterator_to_array($this->documents(
      "---\n".
      "- A\n".
      "- B\n"
    )));
  }

  #[Test]
  public function multiple_documents() {
    Assert::equals([['A', 'B'], ['C', 'D']], iterator_to_array($this->documents(
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
    Assert::equals([['A', 'B'], null, ['C', 'D']], iterator_to_array($this->documents(
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
    Assert::equals([['A', 'B'], ['C', 'D']], iterator_to_array($this->documents(
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