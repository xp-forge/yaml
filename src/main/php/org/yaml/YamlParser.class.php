<?php namespace org\yaml;

use lang\IllegalArgumentException;
use util\{Bytes, Date};

class YamlParser {
  private $identifiers= [];

  /**
   * Parse a value
   *
   * @param  org.yaml.Input $reader
   * @param  string $value
   * @param  int $level
   * @return var
   */
  public function valueOf($reader, $value, $level= 0) {
    if (null === $value) {
      if (null === ($line= $reader->nextLine())) return null;

      // Check whether the next line at same or lesser indentation level. This
      // means we have a line like "key:\n" which means we're encountering an 
      // empty value, which means NULL. If we find elements indented at the same
      // level, they're either a sequence (- value) or a map (key: value).
      $spaces= strspn($line, ' ');
      if ($spaces < $level) {
        $reader->resetLine($line);
        return null;
      }

      $r= [];
      $id= 0;
      do {
        $p= strspn($line, ' ');
        $l= strlen($line);
        if ($p === $l) {
          continue;
        } else if ('#' === $line[$p]) {
          continue;
        } else if ($p < $spaces) {
          break;
        } else if ('---' === $line || '...' === $line) {
          break;
        }

        // Sequences (begin with a dash) and maps (key: value). A special case is the 
        // compacted mapping, where the first key starts right inside a sequence, e.g.
        // "- one: two\n  three: four"
        if ('-' === $line[$spaces]) {
          $key= $id++;
          if (!strpos('*&', $line[$spaces + 2] ?? '-') && strpos($line, ': ')) {
            $reader->resetLine(str_repeat(' ', $spaces + 2).substr($line, $spaces + 2));
            $r[$key]= $this->valueOf($reader, null, $spaces);
          } else if ($spaces + 2 > $l) {
            $r[$key]= $this->valueOf($reader, null, $spaces);
          } else {
            $r[$key]= $this->valueOf($reader, substr($line, $spaces + 2), $spaces);
          }
        } else if (!strpos('#!?{[', $line[$spaces]) && strpos($line, ':')) {
          $key= $value= null;
          sscanf($line, "%[^:]: %[^\r]", $key, $value);
          $key= trim(substr($key, $spaces), ' ');
          $r[$key]= $this->valueOf($reader, $value, $spaces);
        } else {
          $r= $this->valueOf($reader, $line, $spaces);
        }

      } while (null !== ($line= $reader->nextLine()));
      $reader->resetLine($line);
      return $r ?: null;
    } else if ('&' === $value[0]) {
      if (false === ($o= strpos($value, ' '))) {
        $id= rtrim(substr($value, 1, strcspn($value, '#') - 1));
        return $this->identifiers[$id]= $this->valueOf($reader, null, $level);
      } else {
        $id= substr($value, 1, $o - 1);
        return $this->identifiers[$id]= $this->valueOf($reader, substr($value, $o + 1), $level);
      }
    } else {
      return $this->tokenValue($reader->tokenIn($value));
    }
  }

  /**
   * Returns value for a given token
   *
   * @param  var[] $token An array with tag and value
   * @return var
   * @throws lang.IllegalArgumentException if the tag is unknown
   */
  private function tokenValue($token) {
    switch ($token[0]) {
      case 'str': return (string)$token[1];
      case 'int': return (int)$token[1];
      case 'bool': return (bool)$token[1];
      case 'float': return (float)$token[1];
      case 'date': return new Date($token[1]);
      case 'binary': return new Bytes(base64_decode($token[1]));
      case 'literal': return $token[1];
      case 'null': return null;
      case 'seq': {
        $r= [];
        foreach ($token[1] as $value) {
          $r[]= $this->tokenValue($value);
        }
        return $r;
      }
      case 'map': {
        $r= [];
        foreach ($token[1] as $key => $value) {
          $r[$key]= $this->tokenValue($value);
        }
        return $r;
      }
      case '*':
        $id= $token[1];
        if (isset($this->identifiers[$id])) return $this->identifiers[$id];
        throw new IllegalArgumentException(sprintf(
          'Unresolved reference "%s", have [%s]',
          $id,
          $this->identifiers ? '"'.implode('", "', array_keys($this->identifiers)).'"' : ''
        ));
      default: throw new IllegalArgumentException('Unknown tag "'.$token[0].'"');
    }
  }

  /**
   * Parse a given input source, using the first (or only) document only
   *
   * @param  org.yaml.Input $reader
   * @param  [:var] $identifiers
   * @return var
   */
  public function parse($reader, $identifiers= []) {
    $this->identifiers= $identifiers;
    $reader->rewind();

    // Check for identifiers, e.g. `%YAML 1.2`
    do {
      $line= $reader->nextLine();
    } while ('' !== $line && null !== $line && '%' === $line[0]);

    // Skip over first document start
    if ('---' !== $line) {
      $reader->resetLine($line);
    }

    return $this->valueOf($reader, null, 0);
  }

  /**
   * Parse a given input source, returning all documents
   *
   * @see    https://yaml.org/spec/1.2/spec.html#id2800132
   * @param  org.yaml.Input $reader
   * @param  [:var] $identifiers
   * @return iterable
   */
  public function documents($reader, $identifiers= []) {
    $this->identifiers= $identifiers;
    $reader->rewind();

    // Check for identifiers, e.g. `%YAML 1.2`
    do {
      if (null === ($line= $reader->nextLine())) return;
    } while ('' !== $line && '%' === $line[0]);

    // If the first line is "---", we have a multi-document YAML source
    if ('---' === $line) {
      do {
        yield $this->valueOf($reader, null, 0);

        $line= $reader->nextLine();
        if ('...' === $line) {
          do {
            if (null === ($line= $reader->nextLine())) break 2;
          } while ('' !== $line && '%' === $line[0]);
        }
      } while ('---' === $line);
    } else {
      $reader->resetLine($line);
      yield $this->valueOf($reader, null, 0);
    }
  }
}