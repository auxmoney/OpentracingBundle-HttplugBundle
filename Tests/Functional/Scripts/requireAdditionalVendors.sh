#!/bin/bash

echo "inside requireadditionalvendors"
#rm -rf /package
#mkdir /package
shopt -s extglob
#cp -r !(build|Tests) /package

cd build/testproject/
#composer require auxmoney/opentracing-bundle-php-http-httplug-bundle

# composer config extra.symfony.require "${SYMFONY_VERSION}"
composer config extra.symfony.allow-contrib true
#composer config repositories.origin path /package
composer config use-github-api false

composer require php-http/curl-client # virtual php-http/client-implementation
composer require http-interop/http-factory-guzzle # virtual psr/http-factory-implementation
#composer require php-http/message
#composer require http-interop/http-factory-guzzle
composer require php-http/httplug-bundle
composer require auxmoney/opentracing-bundle-php-http-httplug-bundle


composer fix-recipes
rm -fr vendor/auxmoney/opentracing-bundle-php-http-httplug-bundle/*
cp -r ../../!(build|vendor) vendor/auxmoney/opentracing-bundle-php-http-httplug-bundle
composer dump-autoload
cd ../../

# composer require symfony/flex --no-update # adds flex, suppress composer.lock
# composer install                          # create a lock file
# composer update symfony/*                 # updates to SYMFONY_VERSION, but runs recipes
# git reset --hard                          # reset tracked files
# git clean -df                             # clean up recipe generated files and folders

#cd ../../
