SHELL = /bin/sh
PROJECT = graze/silex-trailing-slash-handler

.PHONY: install clean help
.PHONY: test test-unit test-matrix

.SILENT: help

install: ## Download the depenedencies then build the image :rocket:.
	make 'composer-install --optimize-autoloader --ignore-platform-reqs'
	docker build --tag ${PROJECT}:latest .

composer-%: ## Run a composer command, `make "composer-<command> [...]"`.
	docker run -it --rm \
	-v $$(pwd):/app \
	-v ~/.composer:/root/composer \
	-v ~/.ssh:/root/.ssh:ro \
	composer/composer --ansi --no-interaction $*

test: ## Run the unit testsuites.
test: test-unit

test-unit: ## Run the unit testsuite.
	docker run --rm -it -v $$(pwd):/opt/${PROJECT} ${PROJECT} \
	composer test --ansi

test-matrix:
	docker run --rm -it -v $$(pwd):/opt/${PROJECT} -w /opt/${PROJECT} php:5.6-cli \
	vendor/bin/phpunit --testsuite unit
	docker run --rm -it -v $$(pwd):/opt/${PROJECT} -w /opt/${PROJECT} php:7.0-cli \
	vendor/bin/phpunit --testsuite unit

clean: ## Clean up any images.
	docker rmi ${PROJECT}:latest

help: ## Show this help message.
	echo "usage: make [target] ..."
	echo ""
	echo "targets:"
	fgrep --no-filename "##" $(MAKEFILE_LIST) | fgrep --invert-match $$'\t' | sed -e 's/: ## / - /'
