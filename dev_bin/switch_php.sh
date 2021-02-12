#!/usr/bin/env bash

version="$1"
path="/usr/bin/php${version}"

if [[ $# -eq 0 ]] ; then
    echo "./$(basename "$0") PHP_VERSION[7.1|7.2|7.3|7.4]"
    exit 1
fi

if ! [[ -f "${path}" ]]; then
    echo "php ${version} binary could not be located"
    exit 1
fi

update-alternatives --set php "$path"

if [ $? -ne 0 ]; then
    echo "something went wrong!"
fi

php -v