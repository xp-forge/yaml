<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  public function parse(\io\streams\TextReader $reader) {
    $r= array();
    while (null !== ($line= $reader->readLine())) {
      sscanf($line, "%[^:]: %[^\r]", $key, $value);
      $r[$key]= $value;
    }
    return $r;
  }
}