# registry.gitlab.com/c11k/c11k:latest
# For unit testing and deployment
# Set the base image for subsequent instructions
FROM phpdockerio/php74-fpm:latest
ADD https://github.com/phpredis/phpredis/archive/5.1.0.tar.gz /src/phpredis5.1.0.tar.gz
ADD http://xdebug.org/files/xdebug-2.8.0.tgz /src/xdebug-2.8.0.tgz
ADD auth.json /root/.composer/auth.json
# Update packages
RUN apt-get update \
	&& apt-get -y --no-install-recommends install\
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
        php7.4-phpdbg \
        sqlite \
        unzip \
    && apt-get install -y --only-upgrade php7.4-cli php7.4-common \
    && mkdir -p /src/phpredis \
    && mkdir -p /src/xdebug \
    && tar -xf /src/xdebug-2.8.0.tgz -C /src \
    && cd /src/xdebug-2.8.0 \
    && phpize && ./configure && make && make install \
    && tar -xf /src/phpredis5.1.0.tar.gz -C /src \
    && echo 'zend_extension = /usr/lib/php/20190902/xdebug.so' > /etc/php/7.4/mods-available/xdebug.ini \
    && ln /etc/php/7.4/mods-available/xdebug.ini /etc/php/7.4/cli/conf.d/20-xdebug.ini \
    && ln /etc/php/7.4/mods-available/xdebug.ini /etc/php/7.4/fpm/conf.d/20-xdebug.ini \
    && cd /src/phpredis-5.1.0 \
    && phpize && ./configure && make && make install \
    && echo 'extension=redis.so' > /etc/php/7.4/mods-available/redis.ini \
    && ln /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/cli/conf.d/20-redis.ini \
    && ln /etc/php/7.4/mods-available/redis.ini /etc/php/7.4/fpm/conf.d/20-redis.ini \
    && rm -rf /src/phpredis-5.1.0 \
    && apt-get purge php7.4-dev make \
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install needed extensions
# Here you can install any other extension that you need during the test and deployment process

# Install Laravel Envoy
RUN  composer self-update \
    && composer global require "laravel/envoy=~1.0" \
    && composer clear-cache
