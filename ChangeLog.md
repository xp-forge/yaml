YAML for XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 9.0.0 / ????-??-??

* **Heads up:** Dropped support for PHP < 7.4, see xp-framework/rfc#343
  (@thekid)
* Added PHP 8.5 to test matrix - @thekid

## 8.1.0 / 2024-03-24

* Made compatible with XP 12 - @thekid

## 8.0.0 / 2024-02-04

* Implemented xp-framework/rfc#341: Drop XP <= 9 compatibility - @thekid

## 7.1.0 / 2023-11-04

* Added PHP 8.4 to the test matrix - @thekid
* Merged PR #11: Support quoted and typed keys - @thekid
* Merged PR #10: Migrate to new testing library - @thekid

## 7.0.1 / 2022-10-18

* Fixed nested maps spanning multiple lines in flow syntax - @thekid

## 7.0.0 / 2022-10-18

* Merged PR #8: Implement [Merge Key](https://yaml.org/type/merge.html)
  (@thekid)
* Merged PR #7: Allow passing identifiers to *YamlParser*'s `parse()`
  and `documents()` methods.
  (@thekid)

## 6.0.3 / 2022-02-26

* Fixed "Creation of dynamic property" warnings in PHP 8.2 - @thekid

## 6.0.2 / 2021-10-21

* Made library compatible with XP 11 - @thekid

## 6.0.1 / 2020-10-09

* Fixed *strspn(): Argument #3 ($offset) must be contained...* warning
  (@thekid)

## 6.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  (@thekid)

## 5.2.2 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 5.2.1 / 2019-12-01

* Made compatible with XP 10 - @thekid
* Made compatible with PHP 7.4 by using `[]` for offsets - @thekid

## 5.2.0 / 2019-07-08

* Merge PR #6: Implement support for multiple documents - @thekid

## 5.1.0 / 2019-07-07

* Added support for `!!binary` tag, see https://yaml.org/type/binary.html
  (@thekid)
* Merged PR #5: Refactor parser - fixing various nesting bugs like #4
  (@thekid)

## 5.0.2 / 2019-07-01

* Fixed issue #3: YamlParser::parse() not rewinding input - @thekid

## 5.0.1 / 2018-09-23

* Fixed issue #2: Newlines and comments breaks tree structure - @thekid
* Fixed parsing of `%YAML 1.2` directives embedded in documents - @thekid

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
