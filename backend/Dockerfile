# Backend Laravel dijalankan dengan FrankenPHP (app server berbasis Caddy),
# menggantikan "php artisan serve". Image dasar memakai versi tetap (bukan latest).
FROM dunglas/frankenphp:1.9-php8.2-alpine

# Ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN install-php-extensions pdo_mysql mbstring zip bcmath

# Ambil Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Lapisan cache dependensi ---
# composer.json & composer.lock disalin dan dipasang SEBELUM kode aplikasi
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# --- Lapisan kode aplikasi ---
COPY . .
RUN composer dump-autoload --optimize

# Skrip entrypoint: tunggu DB, migrasi, seed, lalu jalankan FrankenPHP
COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# FrankenPHP (Caddy) mendengarkan pada port 8000
ENV SERVER_NAME=:8000
EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
