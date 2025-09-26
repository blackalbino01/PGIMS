# Use official PHP image with PHP-FPM
FROM php:8.2-fpm

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl libonig-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files to container
COPY . /var/www/html

# Install PHP dependencies with optimized autoloader
RUN composer install --no-dev --optimize-autoloader

# Set write permissions to storage & cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
