#!/bin/bash

rm -rf /package
mkdir /package
shopt -s extglob # to enable extglob
cp -r !(build|Tests) /package

cd build/testproject/

# composer config extra.symfony.require "${SYMFONY_VERSION}"
composer config extra.symfony.allow-contrib true
composer config repositories.origin path /package
# composer config repositories.origin git https://github.com/${PR_ORIGIN}
composer config use-github-api false


composer require php-http/curl-client # virtual php-http/client-implementation
composer require guzzlehttp/psr7
composer require php-http/message
composer require php-http/httplug-bundle
composer require http-interop/http-factory-guzzle

# composer require nyholm/psr7
# composer require auxmoney/opentracing-bundle-jaeger opentracing/opentracing:1.0.0-beta5@beta
composer require auxmoney/opentracing-bundle-php-http-httplug-bundle
cd ../../
