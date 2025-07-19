FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        default-mysql-client \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli \
    && docker-php-ext-install -j$(nproc) gd intl zip exif sockets pcntl opcache \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www && \
    mkdir -p /var/www/html && \
    chown www:www /var/www/html

WORKDIR /var/www/html
USER www
