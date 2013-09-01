<?php namespace org\yaml\unittest;

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

  #[@test]
  public function empty_value() {
    $this->assertEquals(array('key' => null), $this->parse('key: '));
  }

  #[@test, @ignore('Key types not yet supported')]
  public function empty_string_key() {
    $this->assertEquals(array('' => 'value'), $this->parse('!!str : value'));
  }

  #[@test]
  public function empty_string_value() {
    $this->assertEquals(array('key' => ''), $this->parse('key: !!str'));
  }
}