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

echo "==> Running database migrations (auto — no Shell needed)..."
if php artisan migrate --force --no-interaction -v; then
    echo "==> Migrations completed successfully"
    php artisan db:seed --class=Database\\Seeders\\AdminSeeder --force --no-interaction || true
else
    echo "==> ERROR: migrations failed — check DB_* env vars and SSL settings in Render"
    tail -30 storage/logs/laravel.log 2>/dev/null || true
    echo "==> Fallback: cookie session until DB is fixed (avoids 419 on Render)"
    export SESSION_DRIVER=cookie
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
