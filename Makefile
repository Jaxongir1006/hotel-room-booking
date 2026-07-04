# Variables
DOCKER_COMPOSE = docker compose
APP_SERVICE = app

.PHONY: help \
        up down restart build ps logs shell db-fresh docker-test docker-pint docker-wayfinder docker-setup \
        setup dev test lint lint-fix format format-check types-check clean

# Default target
.DEFAULT_GOAL := help

# Colors
CYAN  = \033[36m
GREEN = \033[32m
RESET = \033[0m

help: ## Show this help menu
	@printf "$(GREEN)Hotel Room Booking System - Makefile$(RESET)\n"
	@printf "Usage: make [target]\n\n"
	@printf "$(CYAN)Docker Targets:$(RESET)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## [Dd]ocker:.*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## [Dd]ocker:"}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@printf "\n$(CYAN)Local Development Targets (Without Docker):$(RESET)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## [Ll]ocal:.*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## [Ll]ocal:"}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@printf "\n$(CYAN)General Targets:$(RESET)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## [Gg]eneral:.*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## [Gg]eneral:"}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'

# --- DOCKER TARGETS ---

up: ## Docker: Start all containers in the background
	$(DOCKER_COMPOSE) up -d

down: ## Docker: Stop and remove all containers, networks, and volumes
	$(DOCKER_COMPOSE) down

build: ## Docker: Build or rebuild Docker services
	$(DOCKER_COMPOSE) build

restart: ## Docker: Restart all containers
	$(DOCKER_COMPOSE) restart

ps: ## Docker: List all running containers
	$(DOCKER_COMPOSE) ps

logs: ## Docker: Tail container logs (e.g. make logs svc=app)
	$(DOCKER_COMPOSE) logs -f $(svc)

shell: ## Docker: Start sh shell session inside the app container
	$(DOCKER_COMPOSE) exec -it $(APP_SERVICE) sh

db-fresh: ## Docker: Reset database, re-run all migrations and seeders inside container
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan migrate:fresh --seed

docker-test: ## Docker: Run tests inside the container
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan test --compact

docker-pint: ## Docker: Format PHP code inside the container using Laravel Pint
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) vendor/bin/pint --format agent

docker-wayfinder: ## Docker: Generate Wayfinder TypeScript routes inside the container
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan wayfinder:generate --with-form

docker-setup: build up ## Docker: Setup application from scratch in Docker
	@echo "Waiting for containers to initialize..."
	sleep 5
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan db:seed
	$(DOCKER_COMPOSE) exec $(APP_SERVICE) php artisan wayfinder:generate --with-form
	@echo "Setup complete! Application available at http://localhost:8000"

# --- LOCAL DEVELOPMENT TARGETS ---

setup: ## Local: Perform first-time setup for local development (install packages, build assets)
	composer run setup

dev: ## Local: Start local development servers (PHP server, queue listener, vite, and logs)
	composer run dev

test: ## Local: Run feature and unit tests
	composer run test

lint: ## Local: Check PHP code style using Laravel Pint
	composer run lint:check

lint-fix: ## Local: Auto-fix PHP code style using Laravel Pint
	composer run lint

format: ## Local: Auto-format JS/Vue/CSS files with Prettier
	npm run format

format-check: ## Local: Check JS/Vue/CSS formatting with Prettier
	npm run format:check

types-check: ## Local: Run TypeScript compile-time checks
	npm run types-check

# --- GENERAL TARGETS ---

clean: ## General: Clear application caches and generated build artifacts
	-php artisan config:clear
	-php artisan route:clear
	-php artisan view:clear
	-php artisan cache:clear
	rm -rf public/build
	rm -rf bootstrap/cache/*.php
