#!/usr/bin/env bash

/usr/local/bin/php bin/console cache:clear

php-fpm --nodaemonize