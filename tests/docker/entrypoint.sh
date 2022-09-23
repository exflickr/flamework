#!/usr/bin/env bash

/etc/init.d/mysql start
/etc/init.d/memcached start

exec apachectl -D FOREGROUND