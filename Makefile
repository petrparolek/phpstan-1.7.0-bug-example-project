.PHONY: install qa cs csf phpstan tests coverage-clover coverage-html

install:
	composer install\

qa: phpstan cs

cs:
	vendor/bin/codesniffer app

csf:
	vendor/bin/codefixer app

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon app
