#!/bin/sh
set -e

echo "==================================================="
echo "  Agenda eGov - Railway Deployment"
echo "==================================================="

cd /app

# Create storage directories if not exist
echo "[1/6] Creating storage directories..."
mkdir -p storage/logs
mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "[2/6] Generating application key..."
    php artisan key:generate --force --no-interaction
else
    echo "[2/6] APP_KEY already set, skipping..."
fi

# Run migrations
echo "[3/6] Running database migrations..."
php artisan migrate --force --no-interaction || echo "Migration warning (may be OK if tables exist)"

# Cache configuration for production
echo "[4/6] Caching configuration..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Create storage link
echo "[5/6] Creating storage link..."
php artisan storage:link --force --no-interaction 2>/dev/null || true

# Clear old caches and optimize
echo "[6/6] Optimizing application..."
php artisan optimize --no-interaction

echo "==================================================="
echo "  Application ready! Starting services..."
echo "==================================================="

# Start supervisord (manages nginx + php-fpm + scheduler)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
