#!/usr/bin/env bash

set -eux

/etc/init.d/memcached start

/etc/init.d/mysql start
mysql -e 'CREATE DATABASE IF NOT EXISTS flamework;'
mysql -Dflamework < schema/db_main.schema

exec apachectl -D FOREGROUND