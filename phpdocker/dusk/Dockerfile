# registry.gitlab.com/c11k/serviceandgoods
# For unit testing and deployment
# Set the base image for subsequent instructions
FROM registry.gitlab.com/c11k/serviceandgoods

RUN apt-get update \
	&& apt-get -y --no-install-recommends install \
		chromium-browser \
   && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
