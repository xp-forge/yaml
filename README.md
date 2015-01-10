YAML parser
===========

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/yaml.svg)](http://travis-ci.org/xp-forge/yaml)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/yaml/version.png)](https://packagist.org/packages/xp-forge/yaml)

* See http://www.yaml.org/
* See http://www.yaml.org/spec/1.2/spec.html

Usage example
-------------

```php
use org\yaml\YamlParser;
use org\yaml\FileInput;


$result= (new YamlParser())->parse(new FileInput($argv[1]));
// [
//   language => "php"
//   php => [5.4, 5.5, 5.6]
//   before_script => [
//     "wget 'https://github.com/xp-framework/xp-runners/releases/download/v5.2.0/setup' -O - | php",
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