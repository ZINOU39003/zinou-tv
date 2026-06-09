FROM php:8.2-fpm

# تثبيت المكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تحديد مجلد العمل
WORKDIR /var/www

# نسخ المشروع
COPY . .

# الانتقال إلى backend
WORKDIR /var/www/backend

# تثبيت Dependencies
RUN composer install --no-dev --optimize-autoloader

# بناء Cache
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# عرض المنفذ
EXPOSE 8000

# تشغيل
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
