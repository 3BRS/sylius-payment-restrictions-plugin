.PHONY: run init var yarn ci fix ecs bash static cache behat tests

MAKEFLAGS += --no-print-directory # to disable "make: Entering directory ..." messages

run: init

init:
	which docker > /dev/null || (echo "Please install docker binary" && exit 1)
	if command -v direnv &> /dev/null; then \
		[ -f .envrc ] || cp .envrc.dist .envrc; \
		direnv allow; \
	fi
	docker compose up -d
	./bin-docker/composer update --no-interaction --no-plugins
	@make var
	./bin-docker/php ./bin/console doctrine:database:create --no-interaction --if-not-exists
	./bin-docker/php ./bin/console doctrine:migrations:migrate --no-interaction
	./bin-docker/php ./bin/console doctrine:schema:update --force --no-interaction
	./bin-docker/php ./bin/console doctrine:migration:sync-metadata-storage
	./bin-docker/php ./bin/console assets:install
	./bin-docker/yarn --cwd=tests/Application install --pure-lockfile
	GULP_ENV=prod ./bin-docker/yarn --cwd=tests/Application build
	@make var

init-tests:
	which docker > /dev/null || (echo "Please install docker binary" && exit 1)
	if command -v direnv &> /dev/null; then \
		[ -f .envrc ] || cp .envrc.dist .envrc; \
		direnv allow; \
	fi
	docker compose up -d
	./bin-docker/composer update --no-interaction --no-plugins
	@make var
	./bin-docker/php ./bin/console --env=test doctrine:database:drop --no-interaction --force --if-exists
	./bin-docker/php ./bin/console --env=test doctrine:database:create --no-interaction --if-not-exists
	./bin-docker/php ./bin/console --env=test doctrine:migrations:migrate --no-interaction
	./bin-docker/php ./bin/console --env=test doctrine:schema:update --force --complete --no-interaction
	./bin-docker/php ./bin/console --env=test doctrine:migration:sync-metadata-storage
	./bin-docker/php ./bin/console --env=test assets:install
	./bin-docker/yarn --cwd=tests/Application install --pure-lockfile
	GULP_ENV=prod ./bin-docker/yarn --cwd=tests/Application build
	@make var

cache:
	@make var
	./bin-docker/php ./bin/console cache:clear
	chmod -R 0777 tests/Application/var

static: fix static-only

static-only:
	@make ecs
	@make phpstan
	@make composer-lint
	@make symfony-lint
	@make doctrine-lint
	@make say-ok

phpstan:
	./bin-docker/docker-bash bin/phpstan.sh

behat: behat-db-setup
	./bin-docker/docker-bash bin/behat.sh

behat-db-setup:
	./bin-docker/php ./bin/console --env=test doctrine:database:drop --force --if-exists
	./bin-docker/php ./bin/console --env=test doctrine:database:create --no-interaction
	./bin-docker/php ./bin/console --env=test doctrine:migrations:migrate --no-interaction
	./bin-docker/php ./bin/console --env=test doctrine:schema:update --force --complete --no-interaction
	./bin-docker/php ./bin/console --env=test cache:warmup

phpunit:
	./bin-docker/php bin/phpunit

ecs:
	./bin-docker/docker-bash bin/ecs.sh

symfony-lint:
	./bin-docker/docker-bash bin/symfony-lint.sh

composer-lint:
	./bin-docker/composer validate --no-check-lock

doctrine-lint:
	./bin-docker/docker-bash bin/doctrine-lint.sh

lint: symfony-lint composer-lint doctrine-lint

yarn-build:
	./bin-docker/yarn --cwd=tests/Application install --pure-lockfile
	GULP_ENV=prod ./bin-docker/yarn --cwd=tests/Application build

yarn: yarn-build

schema-reset:
	./bin-docker/php ./bin/console doctrine:database:drop --force --if-exists
	./bin-docker/php ./bin/console doctrine:database:create --no-interaction
	./bin-docker/php ./bin/console doctrine:migrations:migrate --no-interaction
	./bin-docker/php ./bin/console doctrine:schema:update --force --complete --no-interaction
	./bin-docker/php ./bin/console doctrine:migration:sync-metadata-storage

fix:
	./bin-docker/docker-bash bin/ecs.sh --fix

bare-fixtures:
	@echo "############\nLoading fixtures\n############"
	./bin-docker/php ./bin/console sylius:fixtures:load default --no-interaction

var:
	docker compose run --rm --user root php rm -fr tests/Application/var
	mkdir -p tests/Application/var/log
	mkdir -p tests/Application/public/media/image
	mkdir -p tests/Application/public/media/cache
	touch tests/Application/var/log/test.log
	touch tests/Application/var/log/dev.log
	chmod -R 0777 tests/Application/var
	docker compose run --rm --user root php chmod -R 0777 tests/Application/public/media

fixtures: schema-reset bare-fixtures var

tests: static behat

ci: init-tests tests

say-ok:
	@echo "OK"

php-bash:
	./bin-docker/docker-bash

bash: php-bash
