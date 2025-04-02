SHELL=/bin/bash

# to see all colors, run
# bash -c 'for c in {0..255}; do tput setaf $c; tput setaf $c | cat -v; echo =$c; done'
# the first 15 entries are the 8-bit colors

# define standard colors
ifneq (,$(findstring xterm,${TERM}))
	BLACK        := $(shell tput -Txterm setaf 0)
	RED          := $(shell tput -Txterm setaf 1)
	GREEN        := $(shell tput -Txterm setaf 2)
	YELLOW       := $(shell tput -Txterm setaf 3)
	LIGHTPURPLE  := $(shell tput -Txterm setaf 4)
	PURPLE       := $(shell tput -Txterm setaf 5)
	BLUE         := $(shell tput -Txterm setaf 6)
	WHITE        := $(shell tput -Txterm setaf 7)
	RESET        := $(shell tput -Txterm sgr0)
else
	BLACK        := ""
	RED          := ""
	GREEN        := ""
	YELLOW       := ""
	LIGHTPURPLE  := ""
	PURPLE       := ""
	BLUE         := ""
	WHITE        := ""
	RESET        := ""
endif

# Default stage
VERSION ?= develop
REGISTRY_URI = ghcr.io
IMAGE_PHP    = ${REGISTRY_URI}/ghcr.io/nvietthanh/livestream-app/php
IMAGE_NGINX  = ${REGISTRY_URI}/ghcr.io/nvietthanh/livestream-app/nginx

.DEFAULT_GOAL := help
.PHONY: help
help:
	@echo "Usage:"
	@echo ""
	@echo " $(GREEN)make build [VERSION=develop]  $(RESET)Build docker images"
	@echo " $(GREEN)make push [VERSION=develop]   $(RESET)Push docker images"
	@echo " $(GREEN)make login                    $(RESET)Login docker registry"
	@echo " $(GREEN)make logout                   $(RESET)Logout docker registry"
	@echo " $(GREEN)make help                     $(RESET)Show this help output"
	@echo ""


.PHONY: login
login:
	@echo ">>> Login registry"
	docker login ${REGISTRY_URI}


.PHONY: logout
logout:
	@echo ">>> Logout registry"
	docker logout ${REGISTRY_URI}


.PHONY: clean
clean:
	@echo ">>> Clean workspace ..."
	rm -rf bootstrap/cache/*.php
	rm -rf storage/app/public/*
	rm -rf storage/framework/cache/data/*
	rm -rf storage/framework/sessions/*
	rm -rf storage/framework/testing/*
	rm -rf storage/framework/views/*.php
	rm -rf storage/logs/*.log
	rm -rf storage/*.key
	rm -rf coverage
	rm -rf vendor
	rm -rf node_modules
	rm -rf public/build
	rm -rf public/storage
	# [ -f .env ] && mv .env .env.backup


.PHONY: build build_php build_nginx
build: clean build_php build_nginx
	# [ -f .env.backup ] && [ ! -f .env ] && mv .env.backup .env


.PHONY: build_php
build_php:
	@echo ">>> Building PHP image ..."
	docker build \
		--file ./docker/Dockerfile \
		--tag ${IMAGE_PHP}:${VERSION} \
		--target app \
		.


.PHONY: build_nginx
build_nginx:
	@echo ">>> Building NGINX image ..."
	docker build \
		--file ./docker/Dockerfile \
		--tag ${IMAGE_NGINX}:${VERSION} \
		--target web \
		.


.PHONY: push push_php push_nginx
push: push_php push_nginx


.PHONY: push_php
push_php:
	@echo ">>> Pushing PHP images ..."
	docker push ${IMAGE_PHP}:${VERSION}


.PHONY: push_nginx
push_nginx:
	@echo ">>> Pushing NGINX images ..."
	docker push ${IMAGE_NGINX}:${VERSION}

