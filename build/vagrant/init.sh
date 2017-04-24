#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update
sudo apt-get install -y php5-cli git php5-mcrypt php5-curl
sudo apt-get install -y apache2 libapache2-mod-php5
sudo apt-get install -y mysql-server php5-mysql
sudo apt-get install -y memcached php5-memcache

sudo apt-get install emacs24-nox

rm -rf /var/www/html
ln -fs /vagrant/www /var/www/html

cd /vagrant
chmod 755 www/templates_c

sudo rm /etc/apache2/sites-enabled/000-default.conf
sudo ln -s /vagrant/apache/vagrant-default.conf /etc/apache2/sites-enabled/000-default.conf

sudo a2enmod rewrite
sudo php5enmod mcrypt

/etc/init.d/apache2 start
