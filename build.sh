#!/bin/bash
set -e

echo "📦 Installing backend dependencies..."
cd backend
composer install --no-dev --optimize-autoloader

echo "⚙️ Building Laravel cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build complete!"
