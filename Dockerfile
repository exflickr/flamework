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

# Listen on the HTTP and HTTPS ports
EXPOSE 80
EXPOSE 443

# When the container is run, this script will start mysql and apache,
# and put a sample config in place if necessary
ENTRYPOINT [ "/bin/bash", "tests/docker/entrypoint.sh" ]