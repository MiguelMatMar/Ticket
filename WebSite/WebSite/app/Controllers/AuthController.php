<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\TokenModel;

class AuthController extends Controller {

    /**
     * Muestra la vista de inicio de sesión.
     */
    public function index() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    /**
     * Muestra la vista de registro de usuario.
     */
    public function register() {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    /**
     * Procesa la autenticación del usuario, valida credenciales, gestiona la creación 
     * de sesiones y opcionalmente implementa el inicio de sesión persistente.
     */
    public function login() {
        $email    = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Introduce un email válido');
            $this->redirect('/auth/index');
            return;
        }

        if (empty($password)) {
            $this->flash('error', 'La contraseña es obligatoria');
            $this->redirect('/auth/index');
            return;
        }

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            // Cuenta desactivada por un administrador
            if (!$user['status']) {
                $this->flash('disabled', 'Tu cuenta ha sido desactivada. Contacta con el administrador.');
                $this->redirect('/auth/index');
                return;
            }

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];

            if (isset($_POST['remember_me'])) {
                $token      = bin2hex(random_bytes(32));
                $tokenModel = new TokenModel();
                $tokenModel->createToken($user['id'], $token);
                setcookie('remember_me', $token, time() + (86400 * 30), '/', '', true, true);
            }

            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

            $this->flash('success', 'Bienvenido, ' . $user['nombre']);
            require __DIR__ . '/../Views/auth/login.php';

        } else {
            $this->flash('error', 'Credenciales incorrectas');
            $this->redirect('/auth/index');
        }
    }

    /**
     * Valida los datos del formulario de registro, verifica la existencia previa del correo, 
     * aplica políticas de seguridad para la contraseña y registra al nuevo usuario.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/auth/register');

        $name            = trim(htmlspecialchars($_POST['name'] ?? ''));
        $surnames        = trim(htmlspecialchars($_POST['surnames'] ?? ''));
        $company         = trim(htmlspecialchars($_POST['company'] ?? ''));
        $email           = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $telephoneNumber = trim(htmlspecialchars($_POST['telephoneNumber'] ?? ''));
        $nif             = trim(htmlspecialchars($_POST['nif'] ?? ''));
        $address1        = trim(htmlspecialchars($_POST['address1'] ?? ''));
        $address2        = trim(htmlspecialchars($_POST['address2'] ?? ''));
        $city            = trim(htmlspecialchars($_POST['city'] ?? ''));
        $state           = trim(htmlspecialchars($_POST['state'] ?? ''));
        $postcode        = trim(htmlspecialchars($_POST['postcode'] ?? ''));
        $country         = trim(htmlspecialchars($_POST['country'] ?? ''));
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        if (empty($name) || empty($surnames) || empty($email) || empty($nif) || empty($address1) || empty($city)) {
            $this->flash('error', 'Por favor, rellena todos los campos obligatorios');
            $this->redirect('/auth/register');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->flash('error', 'Las contraseñas no coinciden');
            $this->redirect('/auth/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'El formato de email no es válido');
            $this->redirect('/auth/register');
            return;
        }

        $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W])[A-Za-z\d\W]{8,}$/";
        if (!preg_match($passwordRegex, $password)) {
            $this->flash('error', 'La contraseña debe ser más segura (mín. 8 caracteres, mayúscula, minúscula, número y especial)');
            $this->redirect('/auth/register');
            return;
        }

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $this->flash('error', 'Este email ya está registrado');
            $this->redirect('/auth/register');
            return;
        }

        // Bloquear registro si el email está en la lista de cuentas eliminadas
        $stmt = $db->prepare("SELECT id FROM eliminated_accounts WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $this->flash('error', 'Este email no puede ser utilizado para registrarse');
            $this->redirect('/auth/register');
            return;
        }

        $sql  = "INSERT INTO users (nombre, apellidos, empresa, email, telefono, nif, direccion1, direccion2, ciudad, provincia, codigo_postal, pais, password, ip_registro) 
                VALUES (:name, :surnames, :company, :email, :telephoneNumber, :nif, :address1, :address2, :city, :state, :postcode, :country, :password, :ip)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'name'            => $name,
            'surnames'        => $surnames,
            'company'         => $company,
            'email'           => $email,
            'telephoneNumber' => $telephoneNumber,
            'nif'             => $nif,
            'address1'        => $address1,
            'address2'        => $address2,
            'city'            => $city,
            'state'           => $state,
            'postcode'        => $postcode,
            'country'         => $country,
            'password'        => password_hash($password, PASSWORD_DEFAULT),
            'ip'              => $_SERVER['REMOTE_ADDR']
        ]);

        $this->flash('success', 'Bienvenido de nuevo');
        $this->redirect('/auth/index');
    }

    /**
     * Guarda mensajes temporales en la sesión para ser mostrados en la siguiente petición.
     */
    private function flash($type, $msg) {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    /**
     * Finaliza la sesión del usuario, elimina el token de persistencia si existe 
     * y destruye las variables de sesión actuales.
     */
    public function logout() {
        if (isset($_COOKIE['remember_me'])) {
            $tokenModel = new TokenModel();
            $tokenModel->deleteToken($_COOKIE['remember_me']);
            setcookie('remember_me', '', time() - 3600, '/');
        }

        $_SESSION = [];
        session_destroy();
        $this->redirect('/auth/index');
    }
}