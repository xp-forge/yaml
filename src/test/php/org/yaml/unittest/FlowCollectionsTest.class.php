<?php namespace org\yaml\unittest;

use lang\FormatException;
use test\{Assert, Test, Values};

/**
 * 7.4. Flow Collection Styles
 * ===========================
 *
 * 7.4.1. Flow sequences
 * ---------------------
 * Flow sequence content is denoted by surrounding “[” and “]” characters.
 *
 * 7.4.2. Flow Mappings
 * ---------------------
 * Flow mappings are denoted by surrounding “{” and “}” characters.
 *
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790320
 * @see   http://www.yaml.org/spec/1.2/spec.html#id2790832
 */
class FlowCollectionsTest extends AbstractYamlParserTest {

  #[Test, Values(['[one,two]', '[one, two]', '[ one, two ]', '[ one  ,   two ]', '[ "one", "two" ]', "[ 'one', 'two' ]",])]
  public function seq($declaration) {
    Assert::equals(['one', 'two'], $this->parse($declaration));
  }

  #[Test]
  public function seq_spanning_multiple_lines() {
    Assert::equals(['one', 'two'], $this->parse("[
      one,
      two
    ]"));
  }

  #[Test]
  public function quoted_square_braces() {
    Assert::equals(['[', ']'], $this->parse('[ "[", "]" ]'));
  }

  #[Test]
  public function seq_with_trailing_comma() {
    Assert::equals(['one', 'two'], $this->parse('[ one, two, ]'));
  }

  #[Test]
  public function explicit_seq() {
    Assert::equals(['one', 'two'], $this->parse('!!seq [ one, two ]'));
  }

  #[Test]
  public function explicit_seq_indented() {
    Assert::equals(['one', 'two'], $this->parse("!!seq [\n  one,\n  two\n]"));
  }

  #[Test]
  public function sequence_of_sequences() {
    Assert::equals(
      [
        ['name', 'hr', 'avg'],
        ['Mark McGwire', 65, 0.278],
        ['Sammy Sosa', 63, 0.288]
      ],
      $this->parse(
        "- [name        , hr, avg  ]\n".
        "- [Mark McGwire, 65, 0.278]\n".
        "- [Sammy Sosa  , 63, 0.288]\n"
      )
    );
  }

  #[Test]
  public function nested_sequence() {
    Assert::equals(
      [['one', 'two']],
      $this->parse('[[one, two]]')
    );
  }

  #[Test]
  public function nested_sequences() {
    Assert::equals(
      [['one', 'two'], ['three', 'four']],
      $this->parse('[[one, two], [three, four]]')
    );
  }

  #[Test]
  public function empty_map() {
    Assert::equals(['map' => []], $this->parse('map: {}'));
  }

  #[Test, Values(['{one:two,three:four}', '{ one: two, three: four }', '{ one : two , three : four }', '{ one   :   two , three   :   four }', '{ one:"two", three:"four"}', "{ one:'two', three:'four'}", '{ "one":two, "three":four}', "{ 'one':two, 'three':four}", '{ "one":"two", "three":"four"}', "{ 'one':'two', 'three':'four'}",])]
  public function map($declaration) {
    Assert::equals(['one' => 'two', 'three' => 'four'], $this->parse($declaration));
  }

  #[Test]
  public function map_spanning_multiple_lines() {
    Assert::equals(['one' => 'two', 'three' => 'four'], $this->parse("{
      one   : two,
      three : four
    }"));
  }

  #[Test]
  public function map_with_trailing_comma() {
    Assert::equals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('{ one : two , three: four , }')
    );
  }

  #[Test]
  public function explicit_map() {
    Assert::equals(
      ['one' => 'two', 'three' => 'four'],
      $this->parse('!!map { one : two , three: four }')
    );
  }

  #[Test]
  public function flowstyle_mappings_of_mappings() {
    Assert::equals(
      [
        'Mark McGwire' => ['hr' => 65, 'avg' => 0.278],
        'Sammy Sosa'   => ['hr' => 63, 'avg' => 0.288]
      ],
      $this->parse(
        "Mark McGwire: {hr: 65, avg: 0.278}\n".
        "Sammy Sosa: {\n".
        "    hr: 63,\n".
        "    avg: 0.288\n".
        "  }"
      )
    );
  }

  #[Test]
  public function nested_map() {
    Assert::equals(
      ['map' => ['one' => 'two', 'three' => 'four']],
      $this->parse('{ map: { one : two , three: four } }')
    );
  }

  #[Test]
  public function nested_maps() {
    Assert::equals(
      ['a' => ['one' => 1, 'two' => 2], 'b' => ['three' => 3, 'four' => 4]],
      $this->parse('{ a: { one : 1 , two: 2 }, b: { three: 3, four: 4 } }')
    );
  }

  #[Test]
  public function quoted_curly_braces() {
    Assert::equals(['{' => '}'], $this->parse('{ "{" : "}" }'));
  }

  #[Test]
  public function mongodb_query() {
    Assert::equals(
      [
        ['$project' => [
          'goals' => ['tag' => 1],
          'name'  => 1,
        ]],
        ['$limit' => 1]
      ],
      $this->parse(str_replace("\n        ", "\n", '
        - $project: {
          goals : {
            tag : 1
          },
          name : 1
        }
        - $limit: 1
      '))
    );
  }
}