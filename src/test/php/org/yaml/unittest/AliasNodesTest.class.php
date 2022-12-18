<?php namespace org\yaml\unittest;

use lang\IllegalArgumentException;
use unittest\{Assert, Expect, Test};

/**
 * 7.1 Alias Nodes
 * ===============
 * Subsequent occurrences of a previously serialized node are presented
 * as alias nodes. The first occurrence of the node must be marked by an
 * anchor to allow subsequent occurrences to be presented as alias nodes. 
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2786196
 */
class AliasNodesTest extends AbstractYamlParserTest {

  #[Test]
  public function repeated_nodes() {
    Assert::equals(
      ['Sammy Sosa', 'Sammy Sosa'],
      $this->parse("- &SS Sammy Sosa\n- *SS # Same\n")
    );
  }

  #[Test]
  public function repeated_map_nodes() {
    $person= ['id' => 1549, 'name' => 'Timm'];
    Assert::equals(
      [$person, $person],
      $this->parse("- &person\n  id: 1549\n  name: Timm\n- *person\n")
    );
  }

  #[Test, Expect(class: IllegalArgumentException::class, withMessage: 'Unresolved reference "TF", have ["SS"]')]
  public function unresolved_reference() {
    $this->parse("- &SS Sammy Sosa\n- *TF # Does not exist\n");
  }

  #[Test]
  public function external_reference() {
    Assert::equals(
      ['value' => $this],
      $this->parse("value: *test\n", ['test' => $this])
    );
  }

  #[Test]
  public function anchors_can_be_reused() {
    Assert::equals([
      'First occurrence'  => 'Foo',
      'Second occurrence' => 'Foo',
      'Override anchor'   => 'Bar',
      'Reuse anchor'      => 'Bar'
    ], $this->parse(
      "First occurrence: &anchor Foo\nSecond occurrence: *anchor\n".
      "Override anchor: &anchor Bar\nReuse anchor: *anchor\n"
    ));
  }

  #[Test]
  public function defined_with_flow() {
    Assert::equals(
      [['x' => 1, 'y' => 2], 1],
      $this->parse("- &CENTER { x: 1, y: 2 }\n- &TOP 1")
    );
  }

  #[Test]
  public function used_inside_flow() {
    Assert::equals(
      [10, 1, 'options' => [1, 10]],
      $this->parse("- &BIG 10\n- &SMALL 1\noptions: [ *SMALL, *BIG ]")
    );
  }
}