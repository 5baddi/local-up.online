#!/bin/bash
set -e

cd /var/www

# Generate application key if not already set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Optimise for production
if [ "${APP_ENV}" = "production" ]; then
    echo "Caching config, routes and views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
