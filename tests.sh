#!/usr/bin/env bash
args=""

tty -s && args="-it"

docker compose run ${args} \
  --user=$(id -u) \
 tests ./tests
