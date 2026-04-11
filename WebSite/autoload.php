<?php
// Cargar dependencias de Composer (PHPMailer, etc.)
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        header("Content-Type: text/plain");
        die("Error de Autoload:\nClase: $class\nRuta buscada: $file");
    }
});