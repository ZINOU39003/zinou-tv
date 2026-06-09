FROM php:8.2-apache

# تثبيت المكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تحديد مجلد العمل للمشروع
WORKDIR /app

# نسخ المشروع
COPY . .

# الانتقال لـ backend والتثبيت
WORKDIR /app/backend

RUN composer install --no-dev --optimize-autoloader

# بناء Cache
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# العودة للجذر
WORKDIR /app

# تفعيل mod_rewrite
RUN a2enmod rewrite

# تحديث apache config للإشارة إلى public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /app/backend/public|g' /etc/apache2/sites-available/000-default.conf

# تعيين أذونات
RUN chmod -R 755 /app/backend/storage
RUN chmod -R 755 /app/backend/bootstrap/cache

# عرض المنفذ
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
