FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl libonig-dev libzip-dev zip unzip \
    libpq-dev nginx \
    && docker-php-ext-install pdo_pgsql mbstring zip exif pcntl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
