# Production deployment for Hanaya Shop
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev nginx supervisor \
    libicu-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd opcache intl \
    && pecl install redis && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP-FPM
COPY deployment/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY deployment/php/php.ini /usr/local/etc/php/conf.d/laravel.ini

# Configure Nginx
COPY deployment/nginx/nginx.conf /etc/nginx/nginx.conf
COPY deployment/nginx/https.conf /etc/nginx/sites-available/default

# Configure Supervisor (với queue worker)
COPY deployment/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copy dependency files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy source code
COPY . .
COPY .env.production .env

# Create built frontend assets directory (static assets only)
RUN mkdir -p public/build && \
    echo '{}' > public/build/manifest.json

# Create required directories and set permissions
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/log/nginx \
    /var/log/supervisor \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx

# Run Laravel optimizations (avoid config cache to prevent .env conflicts)
RUN composer dump-autoload --optimize \
    && php artisan storage:link \
    && php artisan view:cache \
    && php artisan route:cache

# Expose ports
EXPOSE 80 443

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

# Start Supervisor (manages Nginx + PHP-FPM)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
