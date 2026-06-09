#!/bin/bash
set -e

echo "📦 Installing PHP & Composer..."
apt-get update
apt-get install -y php php-mysql php-xml php-json php-mbstring curl git

echo "Installing Composer globally..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

echo "📦 Installing backend dependencies..."
cd backend
/usr/local/bin/composer install --no-dev --optimize-autoloader

echo "⚙️ Building Laravel cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build complete!"
