init: init-ci
init-ci: clear \
	docker-pull docker-build create-volumes docker-up \
	api-init

clear: docker-down-clear api-clear

up: docker-up
down: docker-down
restart: down up

create-volumes:
	docker volume create ambulance-mysql
	docker volume create asterisk-postgres

check: lint analyze validate-schema test test-e2e
lint: api-lint
analyze: api-analyze
validate-schema: api-validate-schema
test: api-test api-test-fixtures
test-unit: api-test-unit
test-functional: api-test-functional api-test-fixtures
test-smoke: api-test-fixtures
test-e2e: api-test-fixtures

update-deps: api-composer-update restart

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/* public/uploads/* || true'

api-init: api-permissions api-wait-db \
	api-composer-install \
	api-cache-clear \
	api-migrations \
	jwt-generate-keypair

api-cache-clear:
	docker compose run --rm api-php-cli php -d memory_limit=-1 bin/console cache:clear

api-test-bd-create:
	docker compose run --rm api-php-cli php bin/console doctrine:database:create --env=test

api-test-migrations:
	docker compose run --rm api-php-cli composer app doctrine:migrations:migrate -- --no-interaction --env=test

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 var/cache var/log public || true

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-composer-update:
	docker compose run --rm api-php-cli composer update

api-wait-db:
	docker compose run --rm api-php-cli wait-for-it ambulance-mysql:3306 -t 30

api-migrations:
	docker compose run --rm api-php-cli composer app doctrine:migrations:migrate -- --no-interaction

jwt-generate-keypair:
	docker compose run --rm api-php-cli  php bin/console lexik:jwt:generate-keypair --skip-if-exists

api-fixtures:
	docker compose run --rm api-php-cli composer app hautelook:fixtures:load -- --no-interaction


api-test-fixtures:
	docker compose run --rm api-php-cli composer app hautelook:fixtures:load -- --no-interaction --env=test


api-check: api-validate-schema api-lint api-analyze api-test

api-validate-schema:
	docker compose run --rm api-php-cli composer app doctrine:schema:validate

api-lint:
	docker compose run --rm api-php-cli composer lint
	docker compose run --rm api-php-cli composer php-cs-fixer fix -- --dry-run --diff

cs-fix:
	docker compose run --rm api-php-cli composer php-cs-fixer fix

api-analyze:
	docker compose run --rm api-php-cli composer psalm -- --no-diff

api-analyze-diff:
	docker compose run --rm api-php-cli composer psalm

api-test:
	docker compose run --rm api-php-cli composer test

api-test-coverage:
	docker compose run --rm api-php-cli composer test-coverage

api-test-unit:
	docker compose run --rm api-php-cli composer test -- --testsuite=unit

api-test-unit-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=unit

api-test-functional:
	docker compose run --rm api-php-cli composer test -- --testsuite=functional

api-test-functional-coverage:
	docker compose run --rm api-php-cli composer test-coverage -- --testsuite=functional

build: build-api

build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/e-way.market-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/e-way.market-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/e-way.market-api-php-cli:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-api

push-api:
	docker push ${REGISTRY}/e-way.market-api:${IMAGE_TAG}
	docker push ${REGISTRY}/e-way.market-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/e-way.market-api-php-cli:${IMAGE_TAG}

testing-build: testing-build-testing-api-php-cli

testing-build-testing-api-php-cli:
	docker --log-level=debug build --pull --file=api/docker/testing/php-cli/Dockerfile --tag=${REGISTRY}/e-way.market-testing-api-php-cli:${IMAGE_TAG} api

testing-init:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker compose-testing.yml up -d
	COMPOSE_PROJECT_NAME=testing docker compose -f docker compose-testing.yml run --rm api-php-cli wait-for-it ambulance-mysql:3306 -t 60
	COMPOSE_PROJECT_NAME=testing docker compose -f docker compose-testing.yml run --rm api-php-cli php bin/console doctrine:migrations:migrate --no-interaction
	COMPOSE_PROJECT_NAME=testing docker compose -f docker compose-testing.yml run --rm testing-api-php-cli php bin/console doctrine:fixtures:load --no-interaction
	sleep 15


testing-down-clear:
	COMPOSE_PROJECT_NAME=testing docker compose -f docker compose-testing.yml down -v --remove-orphans

try-testing: try-build try-testing-build try-testing-init try-testing-smoke try-testing-e2e try-testing-down-clear

try-testing-build:
	REGISTRY=localhost IMAGE_TAG=0 make testing-build

try-testing-init:
	REGISTRY=localhost IMAGE_TAG=0 make testing-init

try-testing-smoke:
	REGISTRY=localhost IMAGE_TAG=0 make testing-smoke

try-testing-e2e:
	REGISTRY=localhost IMAGE_TAG=0 make testing-e2e

try-testing-down-clear:
	REGISTRY=localhost IMAGE_TAG=0 make testing-down-clear

swagger:
	docker compose run --rm api-php-cli php bin/console api:openapi:export --yaml --output=swagger.yaml

sh:
	docker compose run --rm api-php-cli sh