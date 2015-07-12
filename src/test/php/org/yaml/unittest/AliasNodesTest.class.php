<?php namespace org\yaml\unittest;

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

  #[@test]
  public function repeated_nodes() {
    $this->assertEquals(
      ['Sammy Sosa', 'Sammy Sosa'],
      $this->parse("- &SS Sammy Sosa\n- *SS # Same\n")
    );
  }

  #[@test]
  public function repeated_map_nodes() {
    $person= ['id' => 1549, 'name' => 'Timm'];
    $this->assertEquals(
      [$person, $person],
      $this->parse("- &person\n  id: 1549\n  name: Timm\n- *person\n")
    );
  }

  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Unresolved reference "TF", have ["SS"]')]
  public function unresolved_reference() {
    $this->parse("- &SS Sammy Sosa\n- *TF # Does not exist\n");
  }

  #[@test]
  public function anchors_can_be_reused() {
    $this->assertEquals([
      'First occurrence'  => 'Foo',
      'Second occurrence' => 'Foo',
      'Override anchor'   => 'Bar',
      'Reuse anchor'      => 'Bar'
    ], $this->parse(
      "First occurrence: &anchor Foo\nSecond occurrence: *anchor\n".
      "Override anchor: &anchor Bar\nReuse anchor: *anchor\n"
    ));
  }
}