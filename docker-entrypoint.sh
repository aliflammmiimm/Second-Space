#!/bin/bash
set -e

# Bersihkan cache konfigurasi saat container boot
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# Jalankan migrasi database otomatis saat pertama kali running
if [ -n "$DB_HOST" ]; then
    echo "Menjalankan migrasi database..."
    php artisan migrate --force || echo "Migrasi dilewati atau gagal koneksi awal."
fi

# Jalankan Apache webserver
exec apache2-foreground
