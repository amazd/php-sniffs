Behance PHP_CodeSniffer Sniffs
==========

Something smells bad.

[List of supported rules](https://github.com/behance/php-sniffs/wiki/Ruleset)

[ruleset.xml defines these](https://github.com/behance/php-sniffs/blob/master/Behance/ruleset.xml)

## To Run
- make sure that you run the `install.sh` script first
  - this places the entire `Behance` directory into `$PEAR_SRC_PATH/PHP/CodeSniffer/Standards/`
  - to ensure that it was installed correctly: `phpcs -i`
- `phpcs --standard=Behance path/to/file(s)`
