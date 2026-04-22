FROM dunglas/frankenphp:php8.2

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        intl \
        zip \
        gd \
        pdo \
        pdo_mysql \
        bcmath \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix permissions (penting di Railway)
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Laravel optimization (optional tapi bagus untuk production)
RUN php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan view:clear || true

# Expose port (Railway akan override, tapi tetap aman)
EXPOSE 8080

# Start server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]