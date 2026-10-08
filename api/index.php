<?php

// Inisialisasi direktori yang dibutuhkan Laravel di folder /tmp
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException("Unable to create serverless runtime directory: $directory");
    }
}

// Load autoloader terlebih dahulu
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Filter dan siapkan packages.php di /tmp agar hanya memuat provider yang terinstall
if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php')) {
    $packages = require __DIR__ . '/../bootstrap/cache/packages.php';
    foreach ($packages as $pkg => $config) {
        if (!empty($config['providers'])) {
            $packages[$pkg]['providers'] = array_values(array_filter(
                $config['providers'],
                fn($p) => class_exists($p)
            ));
        }
    }
    file_put_contents('/tmp/packages.php', '<?php return ' . var_export($packages, true) . ';');
}

// Default environment variables untuk serverless
$defaults = [
    'APP_NAME' => 'DYACARA',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_STORAGE' => '/tmp/storage',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
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
