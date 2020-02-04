# registry.gitlab.com/c11k/serviceandgoods
# For unit testing and deployment
# Set the base image for subsequent instructions
FROM phpdockerio/php74-fpm:latest

ARG PHPREDIS=5.1.1
ARG XDEBUG=2.9.2

# ADD auth.json /root/.composer/auth.json


# Update packages
RUN apt-get update \
	&& apt-get -y --no-install-recommends install \
	    php7.4-dom \
		php7.4-gd \
		php7.4-json \
		php7.4-mbstring \
        php7.4-mysql \
        php7.4-opcache \
        php7.4-pdo \
        php7.4-zip \
        php7.4-sqlite3 \
        sqlite \
        unzip \
    && apt-get install -y --only-upgrade php7.4-cli php7.4-common \
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Laravel Envoy
RUN  composer self-update \
    && composer global require "laravel/envoy=~1.0" \
    && composer clear-cache
