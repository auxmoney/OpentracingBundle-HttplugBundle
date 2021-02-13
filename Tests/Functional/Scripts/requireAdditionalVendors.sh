#!/bin/bash

shopt -s extglob

cd build/testproject/

composer config extra.symfony.allow-contrib true
composer config use-github-api false

composer require php-http/curl-client # virtual php-http/client-implementation
composer require http-interop/http-factory-guzzle # virtual psr/http-factory-implementation
composer require php-http/httplug-bundle
composer require auxmoney/opentracing-bundle-php-http-httplug-bundle

rm -rf vendor/auxmoney/opentracing-bundle-php-http-httplug-bundle/*
cp -r ../../!(build|vendor) vendor/auxmoney/opentracing-bundle-php-http-httplug-bundle
composer dump-autoload
cd ../../
