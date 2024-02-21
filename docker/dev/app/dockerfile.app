FROM php:8.2-apache

RUN pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql



COPY ./desktop /var/www/html
COPY ./../.env /var/www/.env
COPY ./docker/dev/app/php.ini /usr/local/etc/php/


RUN rm -rf /var/www/html/assets/inc/php/API/tmp/*.json

RUN chown -R www-data:www-data /var/www/html
