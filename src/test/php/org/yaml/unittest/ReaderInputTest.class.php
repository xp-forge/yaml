<?php namespace org\yaml\unittest;

use io\streams\{InputStream, MemoryInputStream, TextReader};
use org\yaml\ReaderInput;
use test\Test;

/** Tests the "Reader" input implementation */
class ReaderInputTest extends AbstractInputTest {

  /**
   * Creates a new fixture
   *
   * @param  string $str
   * @return org.yaml.Input
   */
  protected function newFixture($str= '') {
    return new ReaderInput(new TextReader(new MemoryInputStream($str), 'utf-8'));
  }

  #[Test]
  public function rewind_does_not_call_underlying_reset_if_at_beginning() {
    $r= new ReaderInput(new TextReader(new class() implements InputStream {
      public function read($bytes= 8192) { return null; }
      public function available() { return 0; }
      public function close() { }
    }));
    $r->rewind();
  }
}