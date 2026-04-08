<?php
namespace App\Core;

use App\Models\TokenModel;
use App\Core\Database;

class Router {

    public function dispatch() {
        $this->handlePersistentLogin();

        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Limpiamos el rastro del subdirectorio si el servidor lo incluye en la URI
        $url = str_replace('/WebSite/public', '', $url);
        $parts = explode('/', trim($url, '/'));

        if (!empty($parts[0]) && $parts[0] !== 'index.php') {
            $name = ucfirst($parts[0]);
            $controllerName = $name . 'Controller';
        } else {
            // Ajustado: HomeController no existe, usamos Auth
            $controllerName = 'AuthController';
        }

        $methodName = !empty($parts[1]) ? $parts[1] : 'index';
        $controllerClass = "App\\Controllers\\" . $controllerName;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
                return; 
            } else {
                echo "Error: Método $methodName no existe.";
            }
        } else {
            echo "Error: No encuentro el controlador: $controllerClass";
        }
    }

    private function handlePersistentLogin() {
        $tokenModel = new TokenModel();
        $tokenModel->cleanExpiredTokens();

        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
            $userId = $tokenModel->validateToken($_COOKIE['remember_me']);
            if ($userId) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT id, nombre, rol FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nombre'];
                    $_SESSION['user_role'] = $user['rol'];
                }
            }
        }
    }
}