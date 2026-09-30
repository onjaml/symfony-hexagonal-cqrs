DC = docker compose
PHP = $(DC) exec php
CONSOLE = $(PHP) php bin/console

.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Construit les images
	$(DC) build

up: ## Démarre les conteneurs
	$(DC) up -d --wait

down: ## Arrête les conteneurs
	$(DC) down --remove-orphans

logs: ## Affiche les logs
	$(DC) logs -f

sh: ## Ouvre un shell dans le conteneur php
	$(PHP) bash

composer: ## Lance composer, ex: make composer c="require foo/bar"
	$(PHP) composer $(c)

console: ## Lance bin/console, ex: make console c="debug:router"
	$(CONSOLE) $(c)

install: ## Installe les dépendances
	$(PHP) composer install --no-interaction

migrate: ## Joue les migrations
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

fixtures: ## Charge les fixtures (purge la base)
	$(CONSOLE) doctrine:fixtures:load --no-interaction

db-test: ## Prépare la base de test
	$(CONSOLE) doctrine:database:create --if-not-exists --env=test
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --env=test

test: db-test ## Lance la suite PHPUnit
	$(PHP) php bin/phpunit

setup: build up install migrate fixtures ## Installation complète

.PHONY: help build up down logs sh composer console install migrate fixtures db-test test setup
