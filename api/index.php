<?php

// Inisialisasi direktori yang dibutuhkan Laravel di folder /tmp
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

// Default environment variables untuk serverless
// (Vercel mengabaikan blok "env" di vercel.json, sehingga diset di sini sebagai fallback)
$defaults = [
    'APP_NAME' => 'DYACARA',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'true',
    'APP_KEY' => 'base64:Qd/uZP2FlkGyA9Hf+oAA7Mw1AaOZX3Vfk4i0YwSfSfs=',
    'APP_STORAGE' => '/tmp/storage',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'cookie',
    'LOG_CHANNEL' => 'stderr',
    'APP_MAINTENANCE_DRIVER' => 'file',
];

foreach ($defaults as $key => $value) {
    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Teruskan request ke file entrypoint Laravel
require __DIR__ . '/../public/index.php';
