# Simple Laravel Dockerfile for Railway
FROM php:8.2-cli-alpine

# Install dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy app files
COPY . .

# Build
RUN composer dump-autoload --optimize --no-dev
RUN npm ci && npm run build && rm -rf node_modules

# Setup storage
RUN mkdir -p storage/logs storage/framework/{cache/data,sessions,views} bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start command - clear cache first, then rebuild with env vars
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force || true && \
    php artisan storage:link --force || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
