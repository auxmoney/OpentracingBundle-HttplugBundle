#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

readonly name="opentracing-httplug-bundle-docker"

project=$(git rev-parse --show-toplevel)
inner_project="/usr/src/myapp"

docker run -it --rm  -v "$DIR/../":/usr/src/myapp -w $inner_project $name bash
