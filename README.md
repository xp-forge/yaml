YAML parser
===========

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/yaml.svg)](http://travis-ci.org/xp-forge/yaml)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/yaml/version.png)](https://packagist.org/packages/xp-forge/yaml)

* See http://www.yaml.org/
* See http://www.yaml.org/spec/1.2/spec.html

Usage example
-------------

```php
use org\yaml\YamlParser;
use org\yaml\FileInput;

$result= (new YamlParser())->parse(new FileInput('.travis.yml'));
// [
//   language => "php"
//   php => [5.4, 5.5, 5.6]
//   before_script => [
//     "wget 'https://github.com/xp-framework/xp-runners/.../setup' -O - | php",
//     "composer install --prefer-dist",
//     "echo "vendor/autoload.php" > composer.pth",
//     "echo "use=vendor/xp-framework/core" > xp.ini",
//     "echo "[runtime]" >> xp.ini",
//     "echo "date.timezone=Europe/Berlin" >> xp.ini"
//   ]
//   script => ["./unittest src/test/php"]
// ]
//
```

Inputs
------

* `org.yaml.FileInput(io.File|string $in)` - Use file instance or a file name
* `org.yaml.ReaderInput(io.streams.TextReader $in)` - Reads from a text reader
* `org.yaml.StringInput(string $in)` - Input from a string
