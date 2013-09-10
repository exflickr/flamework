#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

apt-get update
apt-get install -y php5-cli git php5-mcrypt php5-curl
apt-get install -y apache2 libapache2-mod-php5
apt-get install -y mysql-server php5-mysql
apt-get install -y memcached php5-memcache

rm -rf /var/www
ln -fs /vagrant/www /var/www

cd /vagrant
chmod 755 www/templates_c
cp www/include/config.php.example www/include/config.php
cat tests/travis/config.php >> www/include/config.php
mysql -e 'CREATE DATABASE flamework;'
mysql -Dflamework < schema/db_main.schema

apt-get install -y php-pear
pear channel-discover pear.phpunit.de
pear install phpunit/PHP_CodeCoverage

apt-get install -y make
pecl install xdebug
echo "zend_extension=/usr/lib/php5/20090626+lfs/xdebug.so" > /etc/php5/conf.d/xdebug.ini

/etc/init.d/apache2 start
