# registry.gitlab.com/c11k/serviceandgoods
# For unit testing and deployment
# Set the base image for subsequent instructions
FROM phpdockerio/php74-fpm:latest

ARG PHPREDIS=5.1.0
ARG XDEBUG=2.8.0

ADD auth.json /root/.composer/auth.json
ADD https://github.com/phpredis/phpredis/archive/${PHPREDIS}.tar.gz /src/phpredis-${PHPREDIS}.tar.gz
ADD http://xdebug.org/files/xdebug-${XDEBUG}.tgz /src/xdebug-${XDEBUG}.tgz

# Update packages
RUN apt-get update \
	&& apt-get -y --no-install-recommends install \
	    make \
	    php7.4-dev \
	    php7.4-dom \
		php7.4-gd \
		php7.4-json \
		php7.4-mbstring \
        php7.4-mysql \
        php7.4-opcache \
        php7.4-zip \
        php-redis \
        php7.4-sqlite3 \
        sqlite \
        unzip \
    && apt-get install -y --only-upgrade php7.4-cli php7.4-common \
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
    && rm -rf /src/ \
    && apt-get purge php7.4-dev make -y\
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install needed extensions
# Here you can install any other extension that you need during the test and deployment process

# Install Laravel Envoy
RUN  composer self-update \
    && composer global require "laravel/envoy=~1.0" \
    && composer clear-cache
