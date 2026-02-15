FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN set -eux; \
    apk add --no-cache icu-dev oniguruma-dev libzip-dev; \
    docker-php-ext-install -j"$(nproc)" mbstring; \
    rm -rf /tmp/*

COPY . /var/www/html

RUN set -eux; \
    chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
