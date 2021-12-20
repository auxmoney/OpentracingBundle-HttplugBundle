#!/bin/bash

shopt -s extglob

cd build/testproject/

composer config extra.symfony.allow-contrib true
composer config use-github-api false

composer require php-http/curl-client # virtual php-http/client-implementation
composer require http-interop/http-factory-guzzle # virtual psr/http-factory-implementation

VENDOR_VERSION=""
CURRENT_REF=${GITHUB_HEAD_REF:-$GITHUB_REF}
CURRENT_BRANCH=${CURRENT_REF#refs/heads/}
if [[ $CURRENT_BRANCH -ne "master" ]]; then
    composer config minimum-stability dev
    VENDOR_VERSION=":dev-${CURRENT_BRANCH}"
fi
composer require auxmoney/opentracing-bundle-php-http-httplug-bundle${VENDOR_VERSION}
composer dump-autoload
cd ../../
