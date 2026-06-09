#!/bin/bash
set -e

cd /app/backend

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan package:discover --ansi || true
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Testing database connection..."
if php artisan migrate --force --no-interaction; then
    echo "==> Migrations completed"
else
    echo "==> WARN: migrations failed — using file session/cache until DB is fixed"
    export SESSION_DRIVER=file
    export CACHE_STORE=file
    export QUEUE_CONNECTION=sync
    php artisan config:clear
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force 2>/dev/null || true

PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
