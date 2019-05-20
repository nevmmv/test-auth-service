#!/usr/bin/make -f

mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
current_dir := $(dir $(mkfile_path))

DOCKER_COMPOSE="docker-compose"

.PHONY: help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

build: clean ## Build the release and develoment container. The development
	eval $(DOCKER_COMPOSE) build --no-cache

up: ## Spin up the project
	eval $(DOCKER_COMPOSE) up -d --force-recreate

stop: ## Stop running containers
	eval $(DOCKER_COMPOSE) stop

rm: stop ## Stop and remove running containers
	eval $(DOCKER_COMPOSE) rm --all

clean: ## Clean the generated/compiles files
	eval $(DOCKER_COMPOSE) stop

test: ## Clean the generated/compiles files
	eval sh test.sh


