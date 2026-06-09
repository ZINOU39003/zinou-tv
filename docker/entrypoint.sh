#!/bin/bash
set -e

cd /app/backend

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan package:discover --ansi || true
php artisan config:clear
php artisan migrate --force --no-interaction || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force 2>/dev/null || true

PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
