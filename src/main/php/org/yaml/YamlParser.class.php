<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  public function parse(\io\streams\TextReader $reader) {
    $r= array();
    while (null !== ($line= $reader->readLine())) {
      sscanf($line, "%[^:]: %[^\r]", $key, $value);
      if (preg_match('/^[+-]?[0-9]+$/', $value)) {
        $r[$key]= (int)$value;
      } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+$/', $value)) {
        $r[$key]= (double)$value;
      } else {
        $r[$key]= $value;
      }
    }
    return $r;
  }
}