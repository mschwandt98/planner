FROM php:8.1.16-apache

RUN apt-get update -y && \
    apt-get install -y libmariadb-dev && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    a2enmod headers && \
    sed -ri -e 's/^([ \t]*)(<\/VirtualHost>)/\1\tHeader set Access-Control-Allow-Origin "*"\n\1\2/g' /etc/apache2/sites-available/*.conf


WORKDIR /var/www/html
