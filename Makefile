.PHONY: default
default: lint

.PHONY: fix
fix: cs-fix lint-fix

.PHONY: lint
lint: lint-composer lint-symfony

.PHONY: lint-composer
lint-composer:
	composer validate --strict
	composer normalize --dry-run

.PHONY: lint-symfony
lint-symfony: lint-symfony-container lint-symfony-twig lint-symfony-xliff lint-symfony-yaml

.PHONY: lint-symfony-container
lint-symfony-container:
	tools/console lint:container

.PHONY: lint-symfony-twig
lint-symfony-twig:
	tools/console lint:twig src tests

.PHONY: lint-symfony-xliff
lint-symfony-xliff:
	tools/console lint:xliff src tests

.PHONY: lint-symfony-yaml
lint-symfony-yaml:
	tools/console lint:yaml src tests

.PHONY: test
test: vendor-bin/tools/vendor
	vendor/bin/phpunit --colors=always

.PHONY: coverage
coverage: vendor-bin/tools/vendor
	vendor/bin/phpunit --colors=always --coverage-clover=build/logs/clover.xml

.PHONY: cs
cs: vendor-bin/tools/vendor
	vendor/bin/php-cs-fixer fix  --verbose --diff --dry-run

.PHONY: cs-fix
cs-fix: vendor-bin/tools/vendor
	vendor/bin/php-cs-fixer fix --verbose

.PHONY: psalm
psalm: vendor-bin/tools/vendor
	vendor/bin/psalm --config=psalm.xml --diff --shepherd --show-info=false --stats --threads=4

.PHONY: phpstan
phpstan: vendor-bin/tools/vendor
	vendor/bin/phpstan analyse

.PHONY: phpmd
phpmd: vendor-bin/tools/vendor
	vendor/bin/phpmd src,tests ansi phpmd.xml

.PHONY: lint-fix
lint-fix:
	find ./src \\( -name '*.xml' -or -name '*.xml.dist' -or -name '*.xlf' \\) -type f -exec xmllint --encode UTF-8 --output '{}' --format '{}' \\;
	find ./src \\( -name '*.yml' -or -name '*.yaml' \\) -not -path '*/vendor/*' | xargs yaml-lint

.PHONY: check-dependencies
check-dependencies: vendor-bin/tools/vendor
	vendor/bin/composer-require-checker check --config-file composer-require.json composer.json

#
# Installation tasks
#

vendor-bin/tools/vendor:
	composer --working-dir=vendor-bin/tools install

