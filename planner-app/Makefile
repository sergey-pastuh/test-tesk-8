# Variables
CONTAINER_APP=planner-app

# Commands
up: build start

up-n-build: build start install migrate

build:
	docker compose build

start:
	docker compose up -d

install:
	docker compose exec $(CONTAINER_APP) composer install
	docker compose exec $(CONTAINER_APP) php artisan key:generate

migrate:
	docker compose exec $(CONTAINER_APP) php artisan migrate || true

down:
	docker compose down -v --remove-orphans

restart: down up

logs:
	docker compose logs -f

bash:
	docker compose exec $(CONTAINER_APP) bash
