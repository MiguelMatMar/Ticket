<?php
spl_autoload_register(function($class) {
    $prefix = 'App\\';
    // Usamos __DIR__ para que la ruta siempre parta de la ubicación de este archivo
    $base_dir = __DIR__ . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));

    // Cambiamos \ por / para compatibilidad con Linux (case-sensitive)
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        // Depuración útil para ver qué está fallando en el servidor
        header("Content-Type: text/plain");
        die("Error de Autoload:\nClase: $class\nRuta buscada: $file");
    }
});