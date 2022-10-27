FROM ubuntu:latest

# Install the packages we need
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git php-cli php-curl php-mbstring apache2 libapache2-mod-php mysql-server php-mysql memcached php-memcache && \
    apt-get autoremove && \
    apt-get clean && apt-get autoclean

# Turn on the rewrite\ssl apache modules
RUN a2enmod rewrite && \
    a2enmod ssl

# TODO: Proper ssl certs via letsencrypt or something

# Configure our path for where we'll serve source-code from
WORKDIR /mnt/flamework
COPY tests/docker/001-flamework.conf /etc/apache2/sites-available/
RUN a2ensite 001-flamework
RUN a2dissite 000-default

RUN rm -rf /var/www/html
RUN ln -fs /mnt/flamework/www /var/www/html

# TODO: PHPUnit via Pear is dead. Also, conditionally install only when running tests?
#RUN apt-get install -y php-pear
#RUN pear channel-discover pear.phpunit.de
#RUN pear install phpunit/PHP_CodeCoverage

# TODO: Only install when running tests?
#RUN apt-get install -y make
#RUN pecl install xdebug
#RUN echo "zend_extension=/usr/lib/php5/20090626+lfs/xdebug.so" > /etc/php5/conf.d/xdebug.ini

# Allow mounting of source code from external to the container
VOLUME ["/mnt/flamework"]

# Optional persistence of the mysql data
VOLUME ["/var/lib/mysql"]

# Listen on the HTTP and HTTPS ports
EXPOSE 80
EXPOSE 443

# When the container is run, this script will start mysql and apache,
# and put a sample config in place if necessary
ENTRYPOINT [ "/bin/bash", "tests/docker/entrypoint.sh" ]