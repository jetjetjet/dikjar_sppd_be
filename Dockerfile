# Backend Laravel 13 (SPPD) - PHP 8.3 FPM
FROM php:8.3-fpm

# Non-interactive apt + locale
ENV DEBIAN_FRONTEND=noninteractive

# System build dependencies (ringan, lapisan terpisah untuk cache)
RUN apt-get update && apt-get install -y --no-install-recommends --no-install-suggests \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libicu-dev \
    unzip \
    git \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions (pdo_pgsql, pgsql, gd, zip, xml, intl)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql gd zip xml intl

# Opcache untuk SAPI CLI juga (dipakai oleh `php artisan serve`) - tanpa ini,
# setiap request meng-compile ulang seluruh framework + vendor dari nol.
RUN { \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/zz-opcache-cli.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# LibreOffice (untuk konversi dokumen Word -> PDF) - lapisan terpisah & opsional
# Jika instalasi ini gagal di jaringan tertentu, komentari dua baris RUN di bawah.
RUN apt-get update && apt-get install -y --no-install-recommends --no-install-suggests \
    libreoffice-writer libreoffice-common \
    && rm -rf /var/lib/apt/lists/*

# Working directory
WORKDIR /var/www/html

# Copy composer files first (untuk layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (tanpa autoload dulu - akan di-dump setelah copy source)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source
COPY . .

# Generate optimized autoloader
# Catatan: public/storage TIDAK di-mkdir manual di sini — path itu reserved
# untuk symlink asli yang dibuat `php artisan storage:link` saat container start
# (lihat entrypoint.sh). Subfolder di storage/app/public (spt/kwitansi/laporan/
# rumming/struk/profile) dibuat otomatis saat runtime oleh Storage facade.
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
