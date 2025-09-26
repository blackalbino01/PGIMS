FROM php:8.2-fpm-alpine

RUN apt-get update && apt-get install -y \
    git curl libonig-dev libzip-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN php artisan optimize:clear

EXPOSE 9000

ENTRYPOINT ["php-fpm"]
