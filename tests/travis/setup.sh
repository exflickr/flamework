#!/bin/sh

chmod 755 www/templates_c
cp www/include/config.php.example www/include/config.php
cat tests/travis/config.php >> www/include/config.php
mysql -e 'CREATE DATABASE flamework;'
mysql -Dflamework < schema/db_main.schema
#printf "\n" | pecl install memcache
