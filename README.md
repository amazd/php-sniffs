Behance PHP_CodeSniffer Sniffs
==========

Something smells bad.

[ruleset.xml](https://github.com/behance/php-sniffs/blob/master/Behance/ruleset.xml)

## To Run (assuming you have `phpcs` v1.5 installed via PEAR)
- make sure that you run the `install.sh` script first
  - this places the entire `Behance` directory into `$PEAR_SRC_PATH/PHP/CodeSniffer/Standards/`
  - to ensure that it was installed correctly: `phpcs -i`
- `phpcs --standard=Behance path/to/file(s)`
