#!/bin/sh
set -e

# Fix permissions on volume-mounted directories (volume mounts override Dockerfile permissions)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key if not set
if grep -q "^APP_KEY=$" .env; then
    php artisan key:generate --no-interaction
fi

# Only the app server (php-fpm) runs migrations and caching — not the queue worker
if [ "$1" = "php-fpm" ]; then
    php artisan migrate --force --no-interaction
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
