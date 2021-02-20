#!/usr/bin/env bash

declare -a symfony_versions=("3.4" "4.4" "5.1")

function join { local IFS="$1"; shift; echo "$*"; }
list_of_symfony_versions=$(join '|' ${symfony_versions[@]})

SYMFONY_VERSION=${1}

if [[ $# -ne 1 ]] || ! [[ "${symfony_versions[@]}" =~ "${symfony_version}" ]]; then

    echo "$(basename "$0") SYMFONY_VERSION[$list_of_symfony_versions]"
    exit 1
fi

export SYMFONY_VERSION

rm -rf build/testproject
docker stop jaeger &> /dev/null
docker rm jaeger
vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/checkEnvironment.sh
vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/setup.sh
Tests/Functional/Scripts/requireAdditionalVendors.sh
vendor/auxmoney/opentracing-bundle-core/Tests/Functional/Scripts/createResetPoint.sh
