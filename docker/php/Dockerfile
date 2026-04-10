FROM php:8.3-fpm-alpine

# Dependances systeme
RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev libwebp-dev \
    libzip-dev zip unzip oniguruma-dev postgresql-dev \
    nodejs npm shadow

# Extensions PHP
RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    mbstring exif pcntl bcmath gd zip \
    opcache sockets

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# User non-root
RUN usermod -u 1000 www-data

WORKDIR /var/www

COPY --chown=www-data:www-data . .

RUN composer install --no-dev --optimize-autoloader \
    && npm ci && npm run build \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
