FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get autoremove
RUN apt-get dist-upgrade
RUN apt-get update
RUN apt-get -y --no-install-recommends install php7.2-mysql php7.2-bcmath php7.2-intl php7.2-soap php7.2-xmlrpc php7.2-xsl php7.2-pgsql php-yaml php7.2-gd php7.2-imagick libfontconfig1 libxrender1 libxext6 rsync acl vim php-imagick
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update
RUN apt-get -y install git
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

