#!/bin/sh
set -e

# Tunggu basis data siap menerima koneksi
echo "Menunggu basis data ${DB_HOST}:${DB_PORT}..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    sleep 2
done
echo "Basis data siap."

# Buat APP_KEY bila belum tersedia
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# Migrasi dan isi data awal
php artisan migrate --force
php artisan db:seed --force

# Bangun ulang cache konfigurasi
php artisan config:cache

# Jalankan server aplikasi
php artisan serve --host=0.0.0.0 --port=8000
