# Laravel 11 için Base Dockerfile

# PHP 8.3 ve Apache kullanan bir temel imaj seçiyoruz
FROM php:8.3-apache

# Uygulama içinde çalışacağımız çalışma dizinini ayarlıyoruz
WORKDIR /var/www/html

# Gerekli sistem bağımlılıklarını ve PHP eklentilerini yüklüyoruz
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd

# Composer kurulumunu yapıyoruz
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Apache için mod_rewrite etkinleştirme
RUN a2enmod rewrite

# Laravel proje dosyalarını kopyalıyoruz
COPY . /var/www/html

# Proje dosyalarının sahipliğini ayarlıyoruz
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel için environment dosyasını kopyalıyoruz
COPY .env.example .env

# Composer üzerinden bağımlılıkları yüklüyoruz
RUN composer install --no-dev --optimize-autoloader

# Laravel için cache işlemleri
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Uygulama portunu açıyoruz
EXPOSE 80

# Başlatma komutunu ayarlıyoruz
CMD ["apache2-foreground"]
