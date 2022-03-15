FROM php:8.1-fpm

RUN apt update && apt install -y \
        zip \
        bash \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
        zip \
        pdo \
        pgsql \
        pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/vhosts/core