DOCKER_COMPOSE 	= docker-compose -f docker/docker-compose.yml -f docker/docker-compose.override.yml
PHP 			= $(DOCKER_COMPOSE) exec -u www-data app php -d memory_limit=-1
COMPOSER 		= $(PHP) /usr/bin/composer
CONSOLE 		= $(PHP) bin/console
YARN 			= $(DOCKER_COMPOSE) run --rm -u node node yarn

##
## Docker stack
## -------
##

build: docker-compose.override.yml 					## Build project images
	@$(DOCKER_COMPOSE) pull --parallel --quiet --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build --pull

clean: down 										## Kill project and remove untracked files
	git clean -ffdX --exclude="!.idea/"

docker-compose:
	@$(DOCKER_COMPOSE) ${c}

docker-sync:
	@if [ -f "docker/docker-sync.yml" ]; then \
		docker-sync ${c} -c docker/docker-sync.yml; \
	fi

docker-sync.yml:
	cp docker/compose/docker-sync.example.yml docker/docker-sync.yml

docker-compose.override.yml:
	@if [ ! -f "docker/docker-compose.override.yml" ]; then \
		if [ -f "docker/docker-sync.yml" ]; then \
			cp docker/compose/docker-compose.sync.example.yml docker/docker-compose.override.yml; \
		else \
			cp docker/compose/docker-compose.override.example.yml docker/docker-compose.override.yml; \
		fi \
	fi

down: docker-compose.override.yml 					## Kill and removes containers and volumes
	@$(DOCKER_COMPOSE) kill
	@$(DOCKER_COMPOSE) down -v --remove-orphans
	@if [ -f "docker/docker-sync.yml" ]; then \
		docker-sync clean -c docker/docker-sync.yml; \
	fi

install: build up database							## Initialize and start project

stop: docker-compose.override.yml 					## Stop project containers
	$(DOCKER_COMPOSE) stop
	@if [ -f "docker/docker-sync.yml" ]; then \
		docker-sync stop -c docker/docker-sync.yml; \
	fi

up: docker-compose.override.yml						## Start project containers
	@if [ -f "docker/docker-sync.yml" ]; then \
		docker-sync start -c docker/docker-sync.yml; \
	fi
	@$(DOCKER_COMPOSE) up -d --force-recreate
	@$(PHP) -r 'echo "Waiting for initial installation ..."; for(;;) { if (false === file_exists("/tmp/DOING_COMPOSER_INSTALL")) { echo " Ready !\n"; break; }}'

.PHONY: build clean docker-compose docker-sync down install stop up

.DEFAULT_GOAL := help

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help

##
## Application
## -------
##

assets: docker-compose.override.yml 				## Compile frontend assets
	@$(DOCKER_COMPOSE) run --rm -u node node

assets-prod: docker-compose.override.yml 			## Compile frontend assets in production mode
	@$(DOCKER_COMPOSE) run --rm -u node node yarn run build:production

cache: docker-compose.override.yml 					## Reset app cache
	@$(CONSOLE) ca:cl
	@$(CONSOLE) ca:wa

composer: 											## Shortcut to use Composer within project app container (ex : make composer c="install --no-suggest")
	@$(COMPOSER) ${c}

console: docker-compose.override.yml 				## Execute command in Symfony console (ex : make console c="ca:cl")
	@$(CONSOLE) ${c}

database: schema migrate

fixtures:
	@$(CONSOLE) --no-interaction doctrine:fixtures:load --append

migrate: 											## Execute migrations on database
	@$(CONSOLE) --no-interaction doctrine:migrations:migrate --allow-no-migration

migration: 											## Generate a new migration based on diff between mapping info and current database
	@$(CONSOLE) doctrine:migrations:diff

schema:
	@$(CONSOLE) doctrine:database:drop --force
	@$(CONSOLE) doctrine:database:create

yarn: 												## Shortcut to use Yarn container (ex : make yarn c="add bootstrap")
	@$(YARN) ${c}

.PHONY: assets cache composer console database migrate migration schema yarn

##
## Tests & QA
## -------
##

check-mapping: 										## Check if ORM mapping files are correct
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app php -d memory_limit=-1 bin/console doctrine:schema:validate --skip-sync

create_test_env:
	@$(DOCKER_COMPOSE) run --rm database sh -c 'mysql -hdatabase -uroot -p$${MYSQL_ROOT_PASSWORD} -e "DROP DATABASE IF EXISTS $${MYSQL_DATABASE}_test; CREATE DATABASE $${MYSQL_DATABASE}_test; GRANT ALL ON $${MYSQL_DATABASE}_test.* TO \"$${MYSQL_USER}\""'
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app sh -c '\
		php -d memory_limit=-1 bin/console --no-interaction doctrine:migrations:migrate --allow-no-migration &&\
		php -d memory_limit=-1 bin/console --no-interaction doctrine:fixtures:load --append'

lint-twig:
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app php -d memory_limit=-1 bin/console lint:twig resources/templates/

lint-yaml:
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app php -d memory_limit=-1 bin/console lint:yaml config/

lints: lint-twig lint-yaml 							## Execute Symfony linters on twig templates and yaml config files

phpcs: 												## Run php-cs-fixer QA tool in dry run mode
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app php -d memory_limit=-1 vendor/bin/php-cs-fixer fix --config=config/.php_cs.dist --dry-run --diff --verbose --allow-risky=yes

phpstan: 											## Run phpstan static code analysis
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app sh -c '\
		php -d memory_limit=-1 vendor/bin/phpstan analyse -c config/.phpstan.neon --level 6 src/ &&\
		php -d memory_limit=-1 vendor/bin/phpstan analyse -c config/.phpstan.neon --level 1 tests/'

phpunit: 											## Run phpunit tests suite
	@$(DOCKER_COMPOSE) run --rm -e APP_ENV=test app php -d memory_limit=-1 vendor/bin/phpunit -c config/phpunit.xml.dist

tests: lints phpcs phpstan create_test_env phpunit 	## Run full QA & tests tools

.PHONY: check-mapping create_test_env lint-twig lint-yaml lints phpcs phpstan phpunit tests
