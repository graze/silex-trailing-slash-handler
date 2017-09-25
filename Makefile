SHELL = /bin/sh
IMAGE ?= graze/php-alpine:test

DOCKER_RUN := docker run --rm -it -v $$(pwd):/srv -w /srv ${IMAGE}

.PHONY: install update update-lowest help clean
.PHONY: test test-unit test-matrix test-matrix-lowest
.PHONY: test-coverage test-coverage-html test-coverage-clover

.SILENT: help

install: ## Install the dependencies
	${MAKE} 'composer-install -o --prefer-dist'

update: ## Update the dependencies
	${MAKE} 'composer-update -o --prefer-dist'

update-lowest: ## Update to the lowest stable dependencies
	${MAKE} 'composer-update -o --prefer-dist --prefer-lowest --prefer-stable'

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	docker run -it --rm \
        -v $$(pwd):/app \
        -v ~/.composer:/tmp \
        -v ~/.ssh:/root/.ssh:ro \
        composer --ansi --no-interaction $*


lint: ## Run phpcs against the code.
	${DOCKER_RUN} vendor/bin/phpcs -p --warning-severity=0 src/ tests/

lint-fix: ## Run phpcsf and fix possible lint errors.
	${DOCKER_RUN} vendor/bin/phpcbf -p src/ tests/

test: ## Run the unit testsuites.
test: test-unit

test-unit: ## Run the unit testsuite.
	${DOCKER_RUN} vendor/bin/phpunit tests/

test-matrix: ## Test in multiple images
	${MAKE} test-unit IMAGE=php:5.6-alpine
	${MAKE} test-unit IMAGE=php:7.0-alpine
	${MAKE} test-unit IMAGE=php:7.1-alpine

test-matrix-lowest: ## Test multiple images
test-matrix-lowest: update-lowest test-matrix update

test-coverage: ## Run all tests and output coverage to the console.
	${DOCKER_RUN} phpdbg7 -qrr vendor/bin/phpunit --coverage-text tests/

test-coverage-html: ## Run all tests and output coverage to html.
	${DOCKER_RUN} phpdbg7 -qrr vendor/bin/phpunit --coverage-html=./tests/report/html tests/

test-coverage-clover: ## Run all tests and output clover coverage to file.
	${DOCKER_RUN} phpdbg7 -qrr vendor/bin/phpunit --coverage-clover=./tests/report/coverage.clover tests/


clean: ## Clean up the local directory
	git clean -X -d -f

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -s ':#'
