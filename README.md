YAML parser
===========

[![Build status on GitHub](https://github.com/xp-forge/yaml/workflows/Tests/badge.svg)](https://github.com/xp-forge/yaml/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/yaml/version.svg)](https://packagist.org/packages/xp-forge/yaml)

* See https://yaml.org/
* See https://yaml.org/spec/1.2.2/
* See https://yaml.org/type/merge.html

Usage example
-------------

```php
use org\yaml\{YamlParser, FileInput};

$result= (new YamlParser())->parse(new FileInput('.travis.yml'));
// [
//   language => "php"
//   php => [7, 7.1, 7.2, 7.3, 7.4, "nightly"]
//   matrix => [
//     allow_failures => [[
//       php => "nightly"
//     ]]
//   ]
//   before_script => ["curl ...", ...]
//   script => ["sh xp-run xp.unittest.TestRunner src/test/php"]
// ]
```

Inputs
------

* `org.yaml.FileInput(io.File|string $in)` - Use file instance or a file name
* `org.yaml.ReaderInput(io.streams.TextReader $in)` - Reads from a text reader
* `org.yaml.StringInput(string $in)` - Input from a string

Multiple documents
------------------

YAML sources can contain more than one document. The `parse()` method will only parse the first (or only) document. To retrieve all documents in a given input, use the iterator returned by `documents()` instead.

```php
use org\yaml\{YamlParser, FileInput};
use util\cmd\Console;

$parser= new YamlParser();
foreach ($parser->documents(new FileInput('objects.yml')) as $i => $document) {
  Console::writeLine('Document #', $i, ': ', $document);
}
```