<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  protected function valueOf($value) {
    if ('true' === $value) {
      return true;
    } else if ('false' === $value) {
      return false;
    } else if ("'" === $value{0}) {
      return substr($value, 1, -1);
    } else if ('0o' === substr($value, 0, 2)) {
      return octdec(substr($value, 2));
    } else if ('0x' === substr($value, 0, 2)) {
      return hexdec(substr($value, 2));
    } else if (preg_match('/^[+-]?[0-9]+$/', $value)) {
      return (int)$value;
    } else if (preg_match('/^[+-]?[0-9]+\.[0-9]+(e\+[0-9]+)?$/', $value)) {
      return (double)$value;
    } else {
      return $value;
    }
  }

  public function parse($reader, $level= 0) {
    $r= array();
    $id= 0;
    while (null !== ($line= $reader->nextLine())) {
      $spaces= strspn($line, ' ');
      if ($spaces > $level) {           // indent
        $reader->resetLine($line);
        $r[$key]= $this->parse($reader, $level + $spaces);
        continue;
      } else if ($spaces < $level) {    // dedent
        $reader->resetLine($line);
        break;
      } else if ('#' === $line{$level}) {
        continue;
      } else if ('-' === $line{$level}) {
        $key= $id++;
        $value= substr($line, $level + 2);
      } else {
        sscanf($line, "%[^:]: %[^\r]", $key, $value);
        $key= substr($key, $level);
      }

      $r[$key]= $this->valueOf($value);
    }
    return $r;
  }
}