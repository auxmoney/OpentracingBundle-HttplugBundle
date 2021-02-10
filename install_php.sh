#!/usr/bin/env bash

declare -a versions=("php7.2" "php7.3" "php7.4")
declare -a extensions=("cli" "apcu-bc" "curl" "json" "mbstring" "opcache" "readline" "xml" "zip" "phpdbg")

for version in "${versions[@]}"; do
    echo "Installing ${version} ..."

    apt-get -y --no-install-recommends install "${version}-apcu" \
        "${version}-apcu-bc" \
        "${version}-cli" \
        "${version}-curl" \
        "${version}-json" \
        "${version}-mbstring" \
        "${version}-opcache" \
        "${version}-readline" \
        "${version}-xml" \
        "${version}-zip" \
        "${version}-phpdbg"
done
