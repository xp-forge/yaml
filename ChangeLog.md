YAML for XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 3.0.0 / 2016-02-22

* Added version compatibility with XP 7 - @thekid

## 2.0.0 / 2015-10-10

* **Heads up: Dropped PHP 5.4 support**. *Note: As the main source is not
  touched, unofficial PHP 5.4 support is still available though not tested
  with Travis-CI*.
  (@thekid)

## 1.0.3 / 2015-07-12

* Rewrote codebase to use short array syntax - @thekid

## 1.0.2 / 2015-07-12

* Added forward compatibility with XP 6.4.0 - @thekid

## 1.0.1 / 2015-02-12

* Changed dependency to use XP ~6.0 (instead of dev-master) - @thekid

## 1.0.0 / 2015-01-10

* Made available via Composer - @thekid

## 0.9.2 / 2014-09-23

* Made installable via Glue - (@thekid)
* Minor bug fixes - (@thekid)

## 0.9.1 / 2013-09-01

* Added org.yaml.StringInput implementation - (@thekid)
* Moved all unittests to org.yaml.unittest - (@thekid)
* Fixed nested flow-style sequences and mappings - (@thekid)

## 0.9.0 / 2013-08-31

* Initial release with support for string, int, float, null, sequences,
  dates and maps, compacted nested mappings, flow- and block styles as 
  well as references. Still missing are custom, map, sequence and set 
  tags, documents, streaming, and non-scalar mapping keys - (@thekid)
