
.PHONY: fix
fix: cbf
	vendor/bin/php-cs-fixer fix

.PHONY: test
test: cs phpunit phpstan

.PHONY: ci
ci: cs phpunit

.PHONY: cs
cs:
	vendor/bin/phpcs

.PHONY: cbf
cbf:
	vendor/bin/phpcbf

.PHONY: phpunit
phpunit:
	vendor/bin/phpunit

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan
