# Dockerfile untuk Railway Deployment
# Laravel 12 + PHP 8.2 + Node.js
# Railway runs as root superuser

FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    nginx \
    supervisor \
    nodejs \
    npm \
    mysql-client

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

# Set working directory
WORKDIR /app

# ============================================
# Build stage - Install dependencies & build
# ============================================
FROM base AS build

WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copy application code
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Install npm dependencies (including devDependencies for vite build)
RUN npm ci

# Build frontend assets
RUN npm run build

# Remove node_modules after build (not needed at runtime)
RUN rm -rf node_modules

# ============================================
# Production stage
# ============================================
FROM base AS production

WORKDIR /app

# Copy built application from build stage
COPY --from=build /app /app

# Create necessary directories
RUN mkdir -p \
    /app/storage/logs \
    /app/storage/framework/cache/data \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/bootstrap/cache \
    /var/log/supervisor \
    /run/nginx

# Set permissions (running as root, so 777 is fine)
RUN chmod -R 777 /app/storage /app/bootstrap/cache

# Copy configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Make entrypoint executable
RUN chmod +x /entrypoint.sh

# Railway uses PORT env variable, default to 8080
ENV PORT=8080

# Expose port
EXPOSE ${PORT}

# Run entrypoint
CMD ["/entrypoint.sh"]
