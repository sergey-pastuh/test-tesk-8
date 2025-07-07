# Variables
CONTAINER_APP=planner-app

# Commands
up: build start

up-n-install: build start install migrate-seed

build:
	docker compose build

start:
	docker compose up -d

install:
	docker compose exec $(CONTAINER_APP) composer install
	docker compose exec $(CONTAINER_APP) php artisan key:generate

migrate:
	docker compose exec $(CONTAINER_APP) php artisan migrate || true

migrate-seed:
	docker compose exec $(CONTAINER_APP) php artisan migrate --seed || true

down:
	docker compose down -v --remove-orphans

restart: down up

logs:
	docker compose logs -f

bash:
	docker compose exec $(CONTAINER_APP) bash

artisan:
	docker compose exec planner-app php artisan $(filter-out $@,$(MAKECMDGOALS))
