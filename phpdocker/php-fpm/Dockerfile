FROM c11k/serviceandgoods:latest
WORKDIR "/application"

## This is to add chromium-browser for dusk testing.
## The dockerfile needs to evolve to have multiple images built upon one-another
## so one image has dusk-testing, one has only xdebug, and one image is minimal.
#RUN apt-get update \
#	&& apt-get -y --no-install-recommends install \
#		chromium-browser \
#   && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
