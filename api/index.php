<?php

// Inisialisasi direktori yang dibutuhkan Laravel di folder /tmp
// Karena sistem file serverless Vercel bersifat read-only selain /tmp
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Konfigurasi path storage dan view compiled ke /tmp
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// Teruskan request ke file entrypoint Laravel
require __DIR__ . '/../public/index.php';
