FROM php:8.2-fpm-alpine

RUN apk update && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    zip \
    unzip \
    git

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN php artisan optimize:clear

EXPOSE 9000

ENTRYPOINT ["php-fpm"]
