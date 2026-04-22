FROM dunglas/frankenphp:php8.2

RUN apt-get update && apt-get install -y \
    git unzip curl \
    libicu-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip gd pdo pdo_mysql bcmath opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

EXPOSE 8080

CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan serve --host=0.0.0.0 --port=8080