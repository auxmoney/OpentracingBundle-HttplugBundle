#!/usr/bin/env bash

# clean any previously created testproject

declare -a php_versions=("7.2" "7.3" "7.4")
declare -a symfony_versions=("3.4" "4.4" "5.1")

function join { local IFS="$1"; shift; echo "$*"; }
list_of_php_versions=$(join '|' ${php_versions[@]})
list_of_symfony_versions=$(join '|' ${symfony_versions[@]})

php_version=${1}
symfony_version=${2}

if [[ $# -ne 2 ]] || ! [[ "${php_versions[@]}" =~ "${php_version}" ]] || ! [[ "${symfony_versions[@]}" =~ "${symfony_version}" ]]; then

    echo "$(basename "$0") PHP_VERSION[$list_of_php_versions] SYMFONY_VERSION[$list_of_symfony_versions]"
    exit 1
fi

docker-compose run -e SYMFONY_VERSION=$symfony_version web bash -c "rm -rf build/testproject && ./switch_php.sh $php_version && docker stop jaeger &> /dev/null || true && docker rm jaeger || true &> /dev/null && vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/checkEnvironment.sh &&
      vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/setup.sh &&
      Tests/Functional/Scripts/requireAdditionalVendors.sh &&
      vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/createResetPoint.sh"