FROM php:8.4-cli

# Gerekli bağımlılıkları yükle
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install zip pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer'ı yükle
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . .

# Composer bağımlılıklarını yükle
RUN composer install --no-dev --optimize-autoloader

# Gerekli dizinleri oluştur ve izinleri ayarla
RUN mkdir -p storage/framework/views storage/framework/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage/framework storage/logs bootstrap/cache

# Laravel'in çalışacağı port
EXPOSE 80

# Entry-point scriptini kopyala
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Başlangıç komutu
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
