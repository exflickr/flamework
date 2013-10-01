#!/bin/bash

env

chmod 755 www/templates_c
cp www/include/config.php.example www/include/config.php

cat tests/travis/config.php >> www/include/config.php
if [ "$TRAVIS_PHP_VERSION" == "5.2" ]; then
	cat tests/travis/config_52.php >> www/include/config.php
fi

mysql -e 'CREATE DATABASE flamework;'
mysql -Dflamework < schema/db_main.schema
#printf "\n" | pecl install memcache
