<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  public function parse(\io\streams\TextReader $reader) {
    $r= array();
    $id= 0;
    while (null !== ($line= $reader->readLine())) {
      if ('-' === $line{0}) {
        $key= $id++;
        $value= substr($line, 2);
      } else {
        sscanf($line, "%[^:]: %[^\r]", $key, $value);
      }
      if ('true' === $value) {
        $r[$key]= true;
      } else if ('false' === $value) {
        $r[$key]= false;
      } else if ("'" === $value{0}) {
        $r[$key]= substr($value, 1, -1);
      } else if ('0o' === substr($value, 0, 2)) {
        $r[$key]= octdec(substr($value, 2));
      } else if ('0x' === substr($value, 0, 2)) {
        $r[$key]= hexdec(substr($value, 2));
      } else if (preg_match('/^[+-]?[0-9]+$/', $value)) {
        $r[$key]= (int)$value;
      } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+(e\+[0-9]+)?$/', $value)) {
        $r[$key]= (double)$value;
      } else {
        $r[$key]= $value;
      }
    }
    return $r;
  }
}