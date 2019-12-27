.PHONY: all lint cs checkdeps phpstan test

all: lint cs test phpstan

lint:
	find ./src \( -name '*.yml' -or -name '*.yaml' \) -not -path '*/vendor/*' | xargs yaml-lint
	find . \( -name '*.xml' -or -name '*.xml.dist' -or -name '*.xlf' \) -not -path '*/vendor/*' -not -path './vendor-bin/*' -not -path '*/node_modules/*' -not -path '*/.*' -type f -exec xmllint --encode UTF-8 --output '{}' --format '{}' \;

cs: vendor
	export PHP_CS_FIXER_IGNORE_ENV=1 && php	vendor/bin/php-cs-fixer fix --verbose

checkdeps: vendor
	vendor/bin/composer-require-checker check composer.json

phpstan: vendor
	vendor/bin/phpstan analyse

test: vendor
	vendor/bin/phpunit -c phpunit.xml.dist --coverage-clover build/logs/clover.xml

vendor: composer.json composer.lock
	composer validate --strict
	composer install
	composer normalize
