FROM c11k/serviceandgoods:latest
WORKDIR "/application"

# Update packages
RUN apt-get update \
	&& apt-get -y --no-install-recommends install \
	    make \
	    php7.4-pcov \
    && apt-get autoremove -y \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

