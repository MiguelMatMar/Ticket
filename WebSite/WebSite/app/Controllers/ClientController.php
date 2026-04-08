<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ClientModel;

class ClientController extends Controller {
    private $clientModel;

    /**
     * Verifica la sesión activa del usuario y realiza la inyección de dependencias 
     * del modelo de cliente.
     */
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
            exit;
        }
        $this->clientModel = new ClientModel();
    }

    /**
     * Prepara y renderiza el panel de control (dashboard) del cliente.
     */
    public function index() {
        $userId = $_SESSION['user_id'];

        $data = [
            'usuario'       => $this->clientModel->getUserData($userId),
            'stats'         => $this->clientModel->getDashboardStats($userId),
            'tickets_lista' => $this->clientModel->getRecentTickets($userId),
            'title'         => "Dashboard"
        ];

        extract($data);
        ob_start();
        require __DIR__ . '/../Views/client/dashboard.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout/main.php';
    }

    /**
     * Renderiza la página de cambiar la contraseña.
     */
    public function changepassword() {
        $userId = $_SESSION['user_id'];

        $data = [
            'usuario' => $this->clientModel->getUserData($userId),
            'title'   => "Cambiar Contraseña"
        ];

        extract($data);
        ob_start();
        require __DIR__ . '/../Views/client/changepassword.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout/main.php';
    }

    /**
     * Procesa el cambio de contraseña del usuario.
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client/changepassword');
            return;
        }

        $userId          = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$this->clientModel->verifyPassword($userId, $currentPassword)) {
            $this->redirect('/client/changepassword?error=current');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $this->redirect('/client/changepassword?error=confirm');
            exit;
        }

        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($passwordRegex, $newPassword)) {
            $this->redirect('/client/changepassword?error=weak');
            exit;
        }

        $success = $this->clientModel->updatePassword($userId, $newPassword);

        if ($success) {
            $this->redirect('/client/changepassword?success=1');
        } else {
            $this->redirect('/client/changepassword?error=db');
        }
        exit;
    }

    /**
     * Renderiza la página de detalles de cuenta.
     */
    public function accdetails() {
        $userId = $_SESSION['user_id'];

        $data = [
            'usuario' => $this->clientModel->getUserData($userId),
            'title'   => "Detalles de la cuenta"
        ];

        extract($data);
        ob_start();
        require __DIR__ . '/../Views/client/accdetails.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout/main.php';
    }

    /**
     * Procesa la actualización de los datos de perfil del usuario.
     */
    public function updateProfile() {
        $id_usuario = $_SESSION['user_id'];

        $data = [
            'nombre'        => $_POST['nombre'] ?? '',
            'apellidos'     => $_POST['apellidos'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'empresa'       => $_POST['empresa'] ?? '',
            'telefono'      => $_POST['telefono'] ?? '',
            'nif'           => $_POST['nif'] ?? '',
            'direccion1'    => $_POST['direccion1'] ?? '',
            'direccion2'    => $_POST['direccion2'] ?? '',
            'ciudad'        => $_POST['ciudad'] ?? '',
            'provincia'     => $_POST['provincia'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'pais'          => $_POST['pais'] ?? 'España',
            'idioma'        => $_POST['idioma'] ?? 'es'
        ];

        if (empty($data['nombre']) || empty($data['apellidos']) || empty($data['email']) || empty($data['nif'])) {
            header('Location: /client/accdetails?error=fields');
            exit;
        }

        $success = $this->clientModel->updateUserData($id_usuario, $data);

        if ($success) {
            $_SESSION['user_name'] = $data['nombre'];
            header('Location: /client/accdetails?success=1');
        } else {
            header('Location: /client/accdetails?error=db');
        }
        exit;
    }
}   