<?php

// Carga el fichero .env si existe (entorno local de desarrollo)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        // Ignorar comentarios
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

return [
    'db' => [
        'host'    => $_ENV['DB_HOST']    ?? 'localhost',
        'name'    => $_ENV['DB_NAME']    ?? '',
        'user'    => $_ENV['DB_USER']    ?? '',
        'pass'    => $_ENV['DB_PASS']    ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ]
];