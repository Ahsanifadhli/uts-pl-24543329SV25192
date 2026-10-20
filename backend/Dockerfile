# Image dasar PHP 8.2 versi tetap (bukan latest)
FROM php:8.2.27-cli-alpine

# Dependensi sistem + ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN apk add --no-cache $PHPIZE_DEPS \
        git \
        unzip \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

# Ambil Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Lapisan cache dependensi ---
# composer.json & composer.lock disalin dan dipasang SEBELUM kode aplikasi,
# supaya lapisan ini dipakai ulang dari cache selama dependensi tidak berubah.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# --- Lapisan kode aplikasi ---
COPY . .
RUN composer dump-autoload --optimize

# Skrip entrypoint: tunggu DB, migrasi, seed, lalu jalankan server
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
