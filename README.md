YAML parser
===========

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/yaml.svg)](http://travis-ci.org/xp-forge/yaml)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)

* See http://www.yaml.org/
* See http://www.yaml.org/spec/1.2/spec.html

Usage example
-------------

```sh
$ xp -w 'create(new \org\yaml\YamlParser())->parse(new \org\yaml\FileInput($argv[1]));' ../xp.public/.travis.yml
[
  language => "php"
  php => [
    0 => 5.3
    1 => 5.4
    2 => 5.5
  ]
  before_script => [
    0 => "wget 'http://xp-framework.net/downloads/releases/bin/setup' -O - | php"
    1 => "echo "use=core:tools" > xp.ini"
    2 => "echo "[runtime]" >> xp.ini"
    3 => "echo "date.timezone=Europe/Berlin" >> xp.ini"
  ]
  script => [
    0 => "(EXCD=0; for i in core/src/test/config/unittest/*.ini; do echo "---> $i"; ./unittest $i; RES=$?; if [ $RES -ne 0 ]; then EXCD=$RES; fi; done; exit $EXCD;)"
  ]
]
```