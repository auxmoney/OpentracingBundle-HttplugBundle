#!/usr/bin/env bash

docker-compose run -e SYMFONY_VERSION=5.1 \
web bash -c "composer update && composer run phpunit-functional"
