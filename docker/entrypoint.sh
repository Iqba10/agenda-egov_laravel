#!/bin/sh

echo "==================================================="
echo "  Agenda eGov - Railway Deployment"
echo "==================================================="
echo "PORT: ${PORT:-8080}"
echo "APP_ENV: ${APP_ENV:-production}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "==================================================="

cd /app

# Create storage directories if not exist
echo "[1/7] Creating storage directories..."
mkdir -p storage/logs
mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p bootstrap/cache
mkdir -p /tmp
chmod -R 777 storage bootstrap/cache /tmp

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "[2/7] Generating application key..."
    php artisan key:generate --force --no-interaction
else
    echo "[2/7] APP_KEY already set, skipping..."
fi

# Test database connection
echo "[3/7] Testing database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database OK'; } catch (Exception \$e) { echo 'DB Error: ' . \$e->getMessage(); }" || echo "DB connection test failed"

# Run migrations
echo "[4/7] Running database migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo "Migration warning (may be OK if tables exist)"

# Cache configuration for production
echo "[5/7] Caching configuration..."
php artisan config:cache --no-interaction 2>&1 || echo "Config cache warning"
php artisan route:cache --no-interaction 2>&1 || echo "Route cache warning"
php artisan view:cache --no-interaction 2>&1 || echo "View cache warning"

# Create storage link
echo "[6/7] Creating storage link..."
php artisan storage:link --force --no-interaction 2>/dev/null || true

echo "[7/7] Setup complete!"

echo "==================================================="
echo "  Starting services on port ${PORT:-8080}..."
echo "==================================================="

# Set default PORT if not provided
export PORT=${PORT:-8080}

# Replace ${PORT} in nginx config using envsubst
envsubst '${PORT}' < /etc/nginx/nginx.conf > /etc/nginx/nginx.conf.tmp
mv /etc/nginx/nginx.conf.tmp /etc/nginx/nginx.conf

# Test nginx config
echo "Testing nginx configuration..."
nginx -t 2>&1 || echo "Nginx config test failed"

# Start supervisord (manages nginx + php-fpm + scheduler)
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
