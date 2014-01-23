Behance PHP_CodeSniffer Sniffs
==========

Something smells bad.

## To Run
```
phpcs --standard=/path/to/this/repo/Behance/ruleset.xml path/to/files
```

Or if it's installed in your installation of phpcs, just run
```
phpcs --standard=Behance path/to/files
```

## To Test
```
cd /path/to/this/repo
composer install && cd tests && phpunit
```
