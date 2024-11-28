# Makefile for Symfony Project
include .env
# Determine if .env.local file exists
ifneq ("$(wildcard .env.local)", "")
	include .env.local
endif

ifndef INSIDE_DOCKER_CONTAINER
	INSIDE_DOCKER_CONTAINER = 0
endif

# Variables
PHP=php
SYMFONY_BIN=bin/console
COMPOSER=composer
CACHE_DIR=var/cache
LOG_DIR=var/log
SRC_DIR=src
HOST_UID := $(shell id -u)
HOST_GID := $(shell id -g)
PHP_USER := -u www-data
PROJECT_NAME := -p ${COMPOSE_PROJECT_NAME}
OPENSSL_BIN := $(shell which openssl)
INTERACTIVE := $(shell [ -t 0 ] && echo 1)
ERROR_ONLY_FOR_HOST = @printf "\033[33mThis command for host machine\033[39m\n"
.DEFAULT_GOAL := help

# Targets
.PHONY: all install clear cache migrate generate-jwt-keys generate-encryption-key exec

exec: ## Execute command in Symfony container
ifeq ($(INSIDE_DOCKER_CONTAINER), 1)
	@bash -c "$(cmd)"
else
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) docker compose $(PROJECT_NAME) exec $(OPTION_T) $(PHP_USER) symfony bash -c "$(cmd)"
endif

all: install generate-database

install:
	$(COMPOSER) install || { echo "Composer install failed!"; exit 1; }

generate-database:
	@echo "Generating Databases..."
	sleep 10 
	$(PHP) $(SYMFONY_BIN) doctrine:database:create

generate-jwt-keys: ## Generate JWT keys
	@echo "Generating JWT keypair..."
	$(PHP) $(SYMFONY_BIN) lexik:jwt:generate-keypair || { echo "JWT keypair generation failed!"; exit 1; }
	@echo "Generating private key..."
	$(OPENSSL_BIN) genrsa -out config/jwt/private.pem 4096 || { echo "Private key generation failed!"; exit 1; }
	@echo "Generating public key..."
	$(OPENSSL_BIN) rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem || { echo "Public key generation failed!"; exit 1; }

generate-encryption-key: ## Generate or use existing encryption key
	@echo "Generating encryption key..."
	$(PHP) $(SYMFONY_BIN) app:generate-encryption-key || { echo "Encryption key generation failed!"; exit 1; }
	@echo "Ensure to set the encryption key in config/secrets/encryption.key if you have one."

clear:
	@echo "Clearing cache..."
	rm -rf $(CACHE_DIR)/*
phpcs: ## Runs PHP CodeSniffer
	@make exec-bash cmd="./vendor/bin/phpcs --version && ./vendor/bin/phpcs --standard=PSR12 --colors -p src tests"

ecs: ## Runs Easy Coding Standard tool
	@make exec-bash cmd="./vendor/bin/ecs --version && ./vendor/bin/ecs --clear-cache check src tests"

ecs-fix: ## Runs Easy Coding Standard tool to fix issues
	@make exec-bash cmd="./vendor/bin/ecs --version && ./vendor/bin/ecs --clear-cache --fix check src tests"

phpunit: ## Runs Test
	@make exec-bash cmd="./vendor/bin/phpunit"

cache:
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Warming up cache..."
	@make exec cmd="php $(SYMFONY_BIN) cache:warmup"
else
	$(ERROR_ONLY_FOR_HOST)
endif

ccm:
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Deleting cache entries..."
	@make exec cmd="php $(SYMFONY_BIN) doctrine:cache:clear-metadata"
else
	$(ERROR_ONLY_FOR_HOST)
endif

cc:
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Deleting cache..."
	@make exec cmd="php $(SYMFONY_BIN) cache:clear"
else
	$(ERROR_ONLY_FOR_HOST)
endif

help: ## Shows available commands with description
	@echo "\033[34mList of available commands:\033[39m"
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "[32m%-27s[0m %s\n", $$1, $$2}'

test: ## Run tests
	@echo "Running tests..."
	$(PHP) vendor/bin/phpunit

build: ## Build dev environment
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose -f compose.yaml build || { echo "Build failed!"; exit 1; }
else
	$(ERROR_ONLY_FOR_HOST)
endif

start: ## Start dev environment
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose -f compose.yaml $(PROJECT_NAME) up -d || { echo "Failed to start containers!"; exit 1; }
else
	$(ERROR_ONLY_FOR_HOST)
endif

stop: ## Stop dev environment containers
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose -f compose.yaml $(PROJECT_NAME) stop
else
	$(ERROR_ONLY_FOR_HOST)
endif

restart: stop build start

down: ## Stop and remove dev environment containers, networks
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose -f compose.yaml $(PROJECT_NAME) down
else
	$(ERROR_ONLY_FOR_HOST)
endif

validate-mysql: ## Validates the entity 
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Validating Doctrine schema..."
	@make exec cmd="php $(SYMFONY_BIN) doctrine:schema:validate"
else
	$(ERROR_ONLY_FOR_HOST)
endif

command: ## run command CheckSignatureStatusCommand
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Executing command..."
	@make exec cmd="php $(SYMFONY_BIN) CheckSignatureStatusCommand"
else
	$(ERROR_ONLY_FOR_HOST)
endif

ssh: ## Get bash inside symfony docker container
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose $(PROJECT_NAME) exec $(OPTION_T) $(PHP_USER) symfony bash
else
	$(ERROR_ONLY_FOR_HOST)
endif

ssh-root: ## Get bash as root user inside symfony docker container
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose $(PROJECT_NAME) exec $(OPTION_T) symfony bash
else
	$(ERROR_ONLY_FOR_HOST)
endif

ssh-mysql: ## Get bash inside mysql docker container
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose $(PROJECT_NAME) exec mysql bash
else
	$(ERROR_ONLY_FOR_HOST)
endif

exec-bash:
ifeq ($(INSIDE_DOCKER_CONTAINER), 1)
	@bash -c "$(cmd)"
else
	@HOST_UID=$(HOST_UID) HOST_GID=$(HOST_GID) WEB_PORT_HTTP=$(WEB_PORT_HTTP) WEB_PORT_SSL=$(WEB_PORT_SSL) XDEBUG_CONFIG=$(XDEBUG_CONFIG) XDEBUG_VERSION=$(XDEBUG_VERSION) MYSQL_VERSION=$(MYSQL_VERSION) INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) SQL_MODE=$(SQL_MODE) docker compose $(PROJECT_NAME) exec $(OPTION_T) $(PHP_USER) symfony bash -c "$(cmd)"
endif

info: ## Shows Php and Symfony version
	@make exec cmd="php --version"
	@make exec cmd="bin/console about"
	@make exec cmd="composer --version"

logs: ## Shows logs from the symfony container. Use ctrl+c in order to exit
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@docker logs -f ${COMPOSE_PROJECT_NAME}-symfony
else
	$(ERROR_ONLY_FOR_HOST)
endif

logs-mysql: ## Shows logs from the mysql container. Use ctrl+c in order to exit
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@docker logs -f ${COMPOSE_PROJECT_NAME}-mysql
else
	$(ERROR_ONLY_FOR_HOST)
endif

update: ## Running Schema Update
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Running Schema Update..."
	@make exec cmd="php $(SYMFONY_BIN) doctrine:schema:update --force"
else
	$(ERROR_ONLY_FOR_HOST)
endif

sync-stripe: ## Running Schema Update
ifeq ($(INSIDE_DOCKER_CONTAINER), 0)
	@echo "Running Sync Stripe..."
	@make exec cmd="php $(SYMFONY_BIN) FeatStripePriceByCsv"
else
	$(ERROR_ONLY_FOR_HOST)
endif