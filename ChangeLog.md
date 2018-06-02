YAML for XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 5.0.0 / 2018-06-02

* **Heads up:** Changed default charset to `utf-8` for `FileInput` and
  `StringInput` implementations. Both classes have optional constructor
  arguments with which a charset can be supplied. Use `NULL` for auto-
  detecting using BOMs.
  (@thekid)

## 4.0.0 / 2018-06-01

* **Heads up:** Dropped PHP 5.5 support - @thekid
* Added compatibility with PHP 7 - @thekid
* Added version compatibility with XP 9 - @thekid

## 3.1.0 / 2016-08-29

* Added version compatibility with XP 8 by refraining from creating an
  anonymous instance of `io.File`
  (@thekid)

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

* Changed dependency to use XP 6.0 (instead of dev-master) - @thekid

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
