SHELL = /bin/sh
IMAGE ?= graze/php-alpine

.PHONY: install clean help
.PHONY: test test-unit test-matrix

.SILENT: help

install: ## Download the dependencies then build the image :rocket:.
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
	composer/composer --ansi --no-interaction $*

test: ## Run the unit testsuites.
test: test-unit

test-unit: ## Run the unit testsuite.
	docker run --rm -it -v $$(pwd):/srv -w /srv ${IMAGE} \
		vendor/bin/phpunit tests/

test-matrix: ## Test in multiple images
	${MAKE} test-unit IMAGE=php:5.6-alpine
	${MAKE} test-unit IMAGE=php:7.0-alpine
	${MAKE} test-unit IMAGE=php:7.1-alpine

test-matrix-lowest: ## Test multiple images
test-matrix-lowest: update-lowest test-matrix update

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
