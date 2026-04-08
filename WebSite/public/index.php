<?php
// /public_html/WebSite/public/index.php
declare(strict_types=1);
session_start();

// El autoload está un nivel arriba de 'public', dentro de 'WebSite'
require_once __DIR__ . '/../autoload.php';

$router = new \App\Core\Router();
$router->dispatch();