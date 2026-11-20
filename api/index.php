<?php

// Pastikan direktori sementara /tmp untuk cache & view serverless tersedia
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Teruskan request ke file index utama Laravel
require __DIR__ . '/../public/index.php';
