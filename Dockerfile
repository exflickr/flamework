FROM ubuntu:14.04

# Install the packages we need
RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y php5-cli git php5-mcrypt php5-curl apache2 libapache2-mod-php5 mysql-server php5-mysql memcached php5-memcache php5-mcrypt && \
    apt-get autoremove && \
    apt-get clean && apt-get autoclean

# Turn on the mcrypt php module and the rewrite apache module
RUN php5enmod mcrypt && \
    a2enmod rewrite

# Configure our paths for where we'll serve source-code from
# TODO: This whole section needs we-work. For example:
# This needs to be a volume mount so code can be edited and loaded live
# but we also need to be able to set up a default config
# TODO: Different config when docker vs travis?
COPY . /mnt/flamework
WORKDIR /mnt/flamework

RUN ln -fs /mnt/flamework/tests/docker/001-flamework.conf /etc/apache2/sites-available/
RUN a2ensite 001-flamework
RUN a2dissite 000-default

RUN rm -rf /var/www/html
RUN ln -fs /mnt/flamework/www /var/www/html

# Templates need to be writable by the web server
RUN chown www-data www/templates_c
RUN chmod 755 www/templates_c

# Put a config that we know will work in place
RUN cp www/include/config.php.example www/include/config.php
RUN cat tests/travis/config.php >> www/include/config.php

# TODO: PHPUnit via Pear is dead. Also, conditionally install only when running tests?
#RUN apt-get install -y php-pear
#RUN pear channel-discover pear.phpunit.de
#RUN pear install phpunit/PHP_CodeCoverage

RUN apt-get install -y make
# TODO: Only install when running tests?
#RUN pecl install xdebug
#RUN echo "zend_extension=/usr/lib/php5/20090626+lfs/xdebug.so" > /etc/php5/conf.d/xdebug.ini

# Allow mounting of source code from external to the container
VOLUME ["/mnt/flamework"]

# Listen on the HTTP and HTTPS ports
EXPOSE 80
EXPOSE 443

# When the container is run, this script will start mysql and apache
ENTRYPOINT [ "/bin/bash", "tests/docker/entrypoint.sh" ]