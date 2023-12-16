
export HOST_USER_ID=$(shell id -u)

PROJECT_NAME=payment-integration-hub
PHPUNIT=vendor/bin/phpunit
PHPSTAN=vendor/bin/phpstan
PINT=vendor/bin/pint

COMPOSE_FILE = docker/docker-compose.yml
RUN_IN_CONTAINER = docker-compose -p $(PROJECT_NAME) -f $(COMPOSE_FILE) run --rm app
RUN_IN_CONTAINER_BASH = $(RUN_IN_CONTAINER) sh -c

############################################################################################################
###	Start / Down / Rebuild Section
############################################################################################################
install: create_configuration composer_install generate_key

create_configuration:
	test -f .env || cp .env.example .env

generate_key:
	$(RUN_IN_CONTAINER) php artisan key:generate

migrate:
	$(RUN_IN_CONTAINER) php artisan migrate

startup: down up migrate

startup-not-detached: down
	docker-compose -p ${PROJECT_NAME} -f docker/docker-compose.yml up

up:
	docker-compose -p ${PROJECT_NAME} -f docker/docker-compose.yml up -d

down:
	docker-compose -p ${PROJECT_NAME} -f docker/docker-compose.yml down -v --remove-orphans

rebuild: down ## Rebuild FPM
	#docker-compose -p ${PROJECT_NAME} -f $(COMPOSE_FILE) pull
	#docker-compose -p ${PROJECT_NAME} -f $(COMPOSE_FILE) build
	docker-compose -p ${PROJECT_NAME} -f $(COMPOSE_FILE) build --pull --force-rm --no-cache

############################################################################################################
### Composer
############################################################################################################

composer_install:  ## Install Composer dependencies
	$(RUN_IN_CONTAINER_BASH) "composer install --no-progress$(ARGS)"

composer_update:
	$(RUN_IN_CONTAINER_BASH) "composer update $(ARGS)"

composer_autoload:
	$(RUN_IN_CONTAINER_BASH) "composer dump-autoload"

composer_no_dev:
	@echo "Removing Dev Dependencies"
	$(RUN_IN_CONTAINER_BASH) "composer install -o --no-dev"

############################################################################################################
### Testing
############################################################################################################

tests: composer_autoload
	@echo ""
	@echo "+++ Run unit tests +++"
	$(RUN_IN_CONTAINER) php $(PHPUNIT) -c phpunit.xml $(ARGS)

phpstan:
	$(RUN_IN_CONTAINER) $(PHPSTAN) analyze -c phpstan.neon --memory-limit=1G

pint_test:
	$(RUN_IN_CONTAINER) $(PINT) app --test

pint:
	$(RUN_IN_CONTAINER) $(PINT) app

