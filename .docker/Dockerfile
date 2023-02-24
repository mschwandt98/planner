FROM php:8.1.16-apache

RUN apt-get update -y && \
    apt-get install -y libmariadb-dev && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
