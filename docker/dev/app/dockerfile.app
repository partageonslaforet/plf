FROM php:8.2-apache

LABEL org.opencontainers.image.source https://github.com/partageonslaforet/plf
LABEL org.opencontainers.image.description DOCKER-DEV

RUN apt-get update && apt-get upgrade
RUN apt-get install -y curl
RUN apt-get install -y net-tools
RUN apt-get install -y telnet
RUN apt-get install -y inetutils-ping
RUN apt-get install -y nmap
RUN apt-get install -y default-mysql-client
RUN apt-get install -y vim
RUN curl --version
RUN apt-get install -y curl

RUN pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql

COPY ./desktop /var/www/html
COPY ./docker/dev/app/php.ini /usr/local/etc/php/

# # Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Set working directory
WORKDIR /var/www/html/assets/inc/php

# # Copy composer files and install dependencies
# COPY composer.json /var/www/html/assets/inc/php
# COPY composer.lock /var/www/html/assets/inc/php

# # Install project dependencies
RUN /usr/local/bin/composer install --no-scripts --no-autoloader --ignore-platform-req=ext-gd






RUN rm -rf /var/www/html/assets/inc/php/API/tmp/*.json

RUN chown -R www-data:www-data /var/www/html
