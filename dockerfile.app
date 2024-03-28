FROM php:8.2-apache


RUN apt-get update && apt-get upgrade

RUN apt-get install -y net-tools
RUN apt-get install -y telnet
RUN apt-get install -y inetutils-ping
RUN apt-get install -y nmap
RUN apt-get install -y default-mysql-client
RUN apt-get install -y vim

RUN pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql


COPY ./desktop /var/www/html
COPY ./docker/dev/app/php.ini /usr/local/etc/php/


RUN rm -rf /var/www/html/assets/inc/php/API/tmp/*.json

RUN chown -R www-data:www-data /var/www/html
