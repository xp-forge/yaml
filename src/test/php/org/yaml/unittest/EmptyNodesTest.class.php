<?php namespace org\yaml\unittest;

use test\{Assert, Ignore, Test};

/**
 * 7.2 Empty Nodes
 * ===============
 * YAML allows the node content to be omitted in many cases. Nodes with
 * empty content are interpreted as if they were plain scalars with an
 * empty value. Such nodes are commonly resolved to a “null” value. 
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2786563
 */
class EmptyNodesTest extends AbstractYamlParserTest {

  #[Test]
  public function empty_value() {
    Assert::equals(['key' => null], $this->parse('key: '));
  }

  #[Test, Ignore('Key types not yet supported')]
  public function empty_string_key() {
    Assert::equals(['' => 'value'], $this->parse('!!str : value'));
  }

  #[Test]
  public function empty_string_value() {
    Assert::equals(['key' => ''], $this->parse('key: !!str'));
  }

  #[Test]
  public function nested_empty_followed_by_toplevel_empty() {
    Assert::equals(['one' => ['sub' => null], 'two' => null], $this->parse("one:\n  sub:\ntwo:\n"));
  }
}