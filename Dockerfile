FROM ubuntu:14.04

RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y php5-cli git php5-mcrypt php5-curl apache2 libapache2-mod-php5 mysql-server php5-mysql memcached php5-memcache php5-mcrypt && \
    apt-get autoremove && \
    apt-get clean && apt-get autoclean

RUN php5enmod mcrypt && \
    a2enmod rewrite

# TODO: This whole section needs we-work. For example:
# This needs to be a volume mount so code can be edited and loaded live
# but we also need to be able to set up a default config
# TODO: Different config when docker vs travis?
# TODO: Looks like we need a new VirtualHost apache config that sets the
# right Directory AllowOverride and other configs. Or how to do another
# approach for that?
COPY . /mnt/flamework
WORKDIR /mnt/flamework

RUN rm -rf /var/www/html
RUN ln -fs /mnt/flamework/www /var/www/html

RUN chown www-data www/templates_c
RUN chmod 755 www/templates_c
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

EXPOSE 80
EXPOSE 443

ENTRYPOINT [ "/bin/bash", "tests/docker/entrypoint.sh" ]