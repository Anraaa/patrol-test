FROM dunglas/frankenphp:php8.2

# Install dependencies + libmagickwand-dev for Imagick
RUN apt-get update && apt-get install -y \
    git unzip curl \
    libicu-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libmagickwand-dev --no-install-recommends \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip gd pdo pdo_mysql bcmath opcache \
    # Install and enable Imagick
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    # Cleanup to keep image small
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Ensure Laravel directory structure and permissions
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

EXPOSE 8080

# Note: In production, it's better to run 'optimize' during build, 
# but keeping your CMD structure for runtime clearing.
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan optimize && \
    php artisan serve --host=0.0.0.0 --port=8080