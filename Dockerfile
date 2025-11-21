# Gunakan PHP dengan Apache
FROM php:8.2-apache

# Install extensions yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    zip unzip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project ke container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy .env example
RUN cp .env.example .env || true

# Generate key
RUN php artisan key:generate

# Beri permission storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ekspos port (Render otomatis ganti dengan $PORT)
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
