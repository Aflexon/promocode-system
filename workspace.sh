#!/usr/bin/env bash
args=""

tty -s && args="-it"

docker compose run ${args} \
 -v "${HOME}/.composer:/.composer" \
 --user=$(id -u) \
 --entrypoint /usr/src/docker-resources/entrypoint.sh \
 app -- "$@"
