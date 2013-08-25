<?php namespace org\yaml;

class YamlParser extends \lang\Object {

  protected function matching($reader, $value, $begin, $end) {
    while (false === ($s= strrpos($value, $end))) {
      if (null === ($line= $reader->nextLine())) {
        throw new \lang\FormatException('Unmatched "'.$begin.'", encountered EOF');
      }
      $value.= $line;
    }
    $offset= strlen($begin);
    return substr($value, $offset, $s - $offset);
  }

  protected function valueOf($reader, $value) {
    if ('true' === $value) {
      return true;
    } else if ('false' === $value) {
      return false;
    } else if ("'" === $value{0}) {
      return substr($value, 1, -1);
    } else if ('{' === $value{0}) {
      return array($this->matching($reader, $value, '{', '}'));
    } else if ('[' === $value{0}) {
      return array($this->matching($reader, $value, '[', ']'));
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

  /**
   * Parse a given input source
   *
   * @param  org.yaml.Input $reader
   * @param  int $level
   * @return var
   */
  public function parse($reader, $level= 0) {
    $r= array();
    $id= 0;
    while (null !== ($line= $reader->nextLine())) {

      // Everything after the comment is ignored
      if (false !== ($comment= strrpos($line, '#'))) {
        $line= rtrim(substr($line, 0, $comment), ' ');
      }

      // Indentation gives structure
      $spaces= strspn($line, ' ');
      if ($spaces === strlen($line)) {
        continue;
      } else if ($spaces > $level) {    // indent
        $reader->resetLine($line);
        $r[$key]= $this->parse($reader, $level + $spaces);
        continue;
      } else if ($spaces < $level) {    // dedent
        $reader->resetLine($line);
        break;
      }

      // Sequences (begin with a dash) and maps (key: value)
      if ('-' === $line{$level}) {
        $key= $id++;
        $r[$key]= $this->valueOf($reader, substr($line, $level + 2));
      } else {
        $key= $value= null;
        sscanf($line, "%[^:]: %[^\r]", $key, $value);
        $r[substr($key, $level)]= $this->valueOf($reader, $value);
      }
    }
    return $r;
  }
}