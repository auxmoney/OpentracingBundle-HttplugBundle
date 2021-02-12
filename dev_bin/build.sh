#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

readonly name="opentracing-httplug-bundle-docker"

docker build -t $name $DIR/.
docker run -it --rm  -v "$DIR/../":/usr/src/myappusr/src/myapp -w /usr/src/myapp $name composer install --dev