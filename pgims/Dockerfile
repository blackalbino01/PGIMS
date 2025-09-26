# Use official PHP 8.2 FPM image
FROM php:8.2-fpm

# Install system dependencies and required PHP extensions
RUN apt-get update && apt-get install -y \
    git curl libonig-dev libzip-dev zip unzip nginx \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Install composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html

# Install dependencies without dev packages and optimize autoloading
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose PHP-FPM port (internal to container)
EXPOSE 9000

# Start PHP-FPM (will be used by nginx)
CMD ["php-fpm"]
