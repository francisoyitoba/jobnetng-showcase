FROM php:8.2-apache

RUN a2enmod rewrite  && docker-php-ext-install pdo pdo_mysql

# Use public/ as document root
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
