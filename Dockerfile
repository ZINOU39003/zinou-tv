FROM php:8.2-apache

# تثبيت المكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ المشروع
COPY backend/ .

# تثبيت Dependencies
RUN composer install --no-dev --optimize-autoloader

# بناء Cache
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# تفعيل mod_rewrite
RUN a2enmod rewrite

# تحديث apache config
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# عرض المنفذ
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
