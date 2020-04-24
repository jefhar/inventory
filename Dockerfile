# registry.gitlab.com/c11k/serviceandgoods
# For unit testing and deployment
# Set the base image for subsequent instructions
FROM phpdockerio/php74-fpm:latest
ARG BUILD_DATE
ARG VCS_REF

LABEL maintainer="Jeff Harris <jeff@jeffharris.us>" \
  org.label-schema.build-date=$BUILD_DATE \
  org.label-schema.description="Inventory system for a goods and services shop." \
  org.label-schema.name="main.serviceandgoods" \
  org.label-schema.schema-version="1.0" \
  org.label-schema.url="https://inventory.jeffharris.us" \
  org.label-schema.vcs-ref=$VCS_REF \
  org.label-schema.vcs-url="https://gitlab.com/c11k/serviceandgoods" \
  PHP="7.4"

# For local use:
#   Sometimes github thinks you're spamming the site too much. Create an auth key
#   and put it in the auth.json.example file and rename it to auth.json then uncomment.
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
        php7.4-redis \
        php7.4-sqlite3 \
        php7.4-zip \
        sqlite \
        unzip \
    && apt-get install -y --only-upgrade php7.4-cli php7.4-common \
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Laravel Envoy
RUN  composer self-update \
    && composer global require "laravel/envoy=~1.0" \
    && composer clear-cache

RUN mkdir /application && ln -s /application /opt/project
