FROM php:8.3.4-fpm

RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql