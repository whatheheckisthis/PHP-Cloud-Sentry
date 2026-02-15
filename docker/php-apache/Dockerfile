FROM php:8.3-apache

RUN a2enmod rewrite headers

WORKDIR /var/www/html
COPY public/ /var/www/html/

EXPOSE 80
