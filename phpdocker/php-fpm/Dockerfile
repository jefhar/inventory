FROM c11k/serviceandgoods:latest
WORKDIR "/application"
ARG PHPREDIS=5.1.1
ARG XDEBUG=2.9.2

ADD https://github.com/phpredis/phpredis/archive/${PHPREDIS}.tar.gz /src/phpredis-${PHPREDIS}.tar.gz
ADD http://xdebug.org/files/xdebug-${XDEBUG}.tgz /src/xdebug-${XDEBUG}.tgz

# Update packages
RUN apt-get update \
	&& apt-get -y --no-install-recommends install \
	    make \
	    php7.4-dev \
    && tar -xf /src/xdebug-${XDEBUG}.tgz -C /src \
    && cd /src/xdebug-${XDEBUG} \
    && phpize && ./configure && make && make install \
    && echo 'zend_extension = /usr/lib/php/20190902/xdebug.so' > /etc/php/7.4/mods-available/xdebug.ini \
    && ln /etc/php/7.4/mods-available/xdebug.ini /etc/php/7.4/cli/conf.d/20-xdebug.ini \
    && ln /etc/php/7.4/mods-available/xdebug.ini /etc/php/7.4/fpm/conf.d/20-xdebug.ini \
    && tar -xf /src/phpredis-${PHPREDIS}.tar.gz -C /src \
    && cd /src/phpredis-${PHPREDIS} \
    && phpize && ./configure && make && make install \
    && echo 'extension=redis.so' > /etc/php/7.4/mods-available/redis.ini \
    && ln /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/cli/conf.d/20-redis.ini \
    && ln /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/fpm/conf.d/20-redis.ini \
    && cd / \
    && rm -rf /src/ \
    && apt-get purge php7.4-dev make -y\
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

## This is to add chromium-browser for dusk testing.
## The dockerfile needs to evolve to have multiple images built upon one-another
## so one image has dusk-testing, one has only xdebug, and one image is minimal.
#RUN apt-get update \
#	&& apt-get -y --no-install-recommends install \
#		chromium-browser \
#   && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
