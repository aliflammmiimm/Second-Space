<?php

// Pastikan direktori sementara /tmp untuk cache & view serverless tersedia
static $initialized = false;
if (!$initialized) {
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
    $initialized = true;
}

// Teruskan request ke file index utama Laravel
require __DIR__ . '/../public/index.php';
