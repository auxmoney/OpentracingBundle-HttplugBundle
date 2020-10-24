#!/usr/bin/env bash

function run_test() {
    php_version=${1:-7.4}
    symfony_version=${2:-5.4}

    php_message="PHP ${php_version}"
    symfony_message="Symfony $symfony_version"

    docker-compose run -e SYMFONY_VERSION=$symfony_version \
        web bash -c "./switch_php.sh ${php_version}; figlet -f big.flf '$php_message';  figlet -f big.flf '$symfony_message'; composer update && composer run phpunit-functional"
}


specific_php_version="$1"
specific_symfony_version="$2"

if [[ ! -z "$specific_php_version" && ! -z "$specific_symfony_version" ]]; then
    run_test "$specific_php_version" "$specific_symfony_version"
    exit
fi

declare -a php_versions=("7.2" "7.3" "7.4")
declare -a symfony_versions=("3.4" "4.4" "5.1")

for php_version in "${php_versions[@]}"; do
    for symfony_version in "${symfony_versions[@]}"; do
        run_test "$php_version" "$symfony_version"
    done
done
