FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    sqlite3 libsqlite3-dev git unzip curl gnupg ca-certificates \
    libpq-dev libzip-dev libonig-dev libjpeg-dev libpng-dev libwebp-dev zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install phpredis via PECL
RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-configure gd --with-jpeg --with-webp && \
    docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \       
        pdo_pgsql \
        pdo_sqlite \
        mbstring \
        zip \
        bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html