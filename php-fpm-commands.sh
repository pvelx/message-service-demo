#!/usr/bin/env bash

cd "`dirname \"$0\"`" && \
 docker-compose exec -T "php-fpm" sh -c "cd /app && $*"