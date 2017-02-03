SPACE_SEPARATED_CHANGED_PHP_FILES := $(shell git diff HEAD HEAD^ --name-only -- "./*.php" "./*composer.*")
COMMA_SEPARATED_CHANGED_PHP_FILES := $(shell echo $(SPACE_SEPARATED_CHANGED_PHP_FILES) | sed 's/ /,/g' )

.PHONY: ci

ci:
	composer install --dev

	# Lint
	./vendor/bin/parallel-lint -j 10 $(SPACE_SEPARATED_CHANGED_PHP_FILES)

	# Static Analysis
	./vendor/bin/phpcs --standard=./Behance -s -n $(SPACE_SEPARATED_CHANGED_PHP_FILES)
	./vendor/bin/phpcpd $(SPACE_SEPARATED_CHANGED_PHP_FILES)
	# @todo: Re-enable phpmd when unused variable $testFile in test suite can be refactored
	# ./vendor/bin/phpmd $(COMMA_SEPARATED_CHANGED_PHP_FILES)  text vendor/behance/phpmd-rules/phpmd.xml.dist

	# Tests
	./vendor/bin/phpunit
