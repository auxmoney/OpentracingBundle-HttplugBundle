#!/usr/bin/env bash

docker-compose run -e SYMFONY_VERSION=$symfony_version \
        web bash -c "./switch_php.sh 7.4; composer update && composer run phpunit-functional"

