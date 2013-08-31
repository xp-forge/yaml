YAML parser
===========
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

Installation
------------

YAML for XP Framework can be installed using the `xpi` utility. The most current release can be discovered as follows:

```sh
$ xpi info -r xp-forge/yaml
@http://builds.planet-xp.net/
Module<xp-forge/yaml>: YAML
https://github.com/xp-forge/yaml
Releases: 1, list {
  0.9.0 (series= "0.9", rc= false, published= "2013-08-31T18:26:47+02:00")
}
```