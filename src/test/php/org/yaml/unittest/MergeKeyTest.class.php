<?php namespace org\yaml\unittest;

use lang\IllegalArgumentException;
use unittest\{Assert, Test};

/**
 * Merge Key Language-Independent Type for YAML™ Version 1.1
 *
 * @see   https://yaml.org/type/merge.html
 */
class MergeKeyTest extends AbstractYamlParserTest {
  private $defines= [
    'CENTER' => ['x' => 1, 'y' => 2],
    'LEFT'   => ['x' => 0, 'y' => 2],
    'BIG'    => ['r' => 10],
    'SMALL'  => ['r' => 1],
  ];
  private $result= [
    'x'     => 1,
    'y'     => 2,
    'r'     => 10,
    'label' => 'center/big'
  ];


  #[Test]
  public function explicit_keys() {
    Assert::equals(
      $this->result,
      $this->parse("x: 1\ny: 2\nr: 10\nlabel: center/big\n", $this->defines)
    );
  }

  #[Test]
  public function merge_one_map() {
    Assert::equals(
      $this->result,
      $this->parse("<< : *CENTER\nr: 10\nlabel: center/big\n", $this->defines)
    );
  }

  #[Test]
  public function merge_multiple_maps() {
    Assert::equals(
      $this->result,
      $this->parse("<< : [ *CENTER, *BIG ]\nlabel: center/big\n", $this->defines)
    );
  }

  #[Test]
  public function override() {
    Assert::equals(
      $this->result,
      $this->parse("<< : [ *BIG, *LEFT, *SMALL ]\nx: 1\nlabel: center/big\n", $this->defines)
    );
  }
}