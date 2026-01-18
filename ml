#!/bin/bash

if [ -t 0 ]; then
  # STDIN — терминал, пайпа нет
  docker exec -it php_enso ./enso ai "$@"
else
  # STDIN приходит из пайпа
  docker exec -i php_enso ./enso ai "$@"
fi
