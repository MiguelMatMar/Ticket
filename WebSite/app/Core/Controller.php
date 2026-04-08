<?php
namespace App\Core;

abstract class Controller {
    
    protected function view(string $view, array $data = []) {
        extract($data);
        
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../Views/layout/main.php';
    }

    protected function redirect(string $route): void {
        header('Location: ' . $route);
        exit;
    }

    protected function authorize(string $requiredRole) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
            exit;
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $requiredRole) {
            $this->redirect('/client/index'); 
            exit;
        }
    }
}