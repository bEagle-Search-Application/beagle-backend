FROM php:8.1.5-fpm-alpine

RUN apk update \
    && apk add autoconf \
    && apk add gcc g++\
    && apk add make \
    && apk add git \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www
RUN chmod 755 /var/www
