#!/usr/bin/env bash

# We want to halt on errors and also print out what we're doing
set -eux

# Start memcached
/etc/init.d/memcached start

# Start mysql and create the database if it doesn't exist
/etc/init.d/mysql start
mysql -e 'CREATE DATABASE IF NOT EXISTS flamework;'
mysql -Dflamework < schema/db_main.schema

# Put the example configuration in place if it doesn't exist
cd /mnt/flamework
if [[ ! -e www/include/config.php ]]; then
    cp www/include/config.php.example www/include/config.php
    perl -i -pe "s/'pass'\t=> 'root',/'pass'\t=> '',/g" www/include/config.php
fi

# Templates need to be writable by the web server
chown www-data www/templates_c
chmod 755 www/templates_c

# Start apache in the foreground so that the container stays running
exec apachectl -D FOREGROUND