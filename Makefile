include docker.mk

.PHONY: test

DRUPAL_VER ?= 8
PHP_VER ?= 7.3

test:
	cd ./tests/$(DRUPAL_VER) && PHP_VER=$(PHP_VER) ./run.sh

dcu:
	docker-compose up -d

dcd:
	docker-compose down
