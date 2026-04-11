<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;

class NotificationController extends Controller {
    private $notificationModel;

    /**
     * Verifica que haya sesión activa y prepara el modelo de notificaciones.
     */
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Devuelve en JSON las notificaciones no leídas del usuario autenticado,
     * junto con el total para mostrar en el badge del navbar.
     */
    public function getNotifications() {
        $userId = $_SESSION['user_id'];
        $notifications = $this->notificationModel->getUnreadByUser($userId);

        header('Content-Type: application/json');
        echo json_encode([
            'total'         => count($notifications),
            'notifications' => $notifications
        ]);
        exit;
    }

    /**
     * Marca como leída una notificación concreta del usuario autenticado.
     * Recibe el ID por POST y devuelve JSON con el resultado.
     */
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $userId         = $_SESSION['user_id'];
        $notificationId = $_POST['id'] ?? null;

        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de notificación requerido']);
            exit;
        }

        $result = $this->notificationModel->markAsRead($notificationId, $userId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }
    /**
     * Devuelve los datos completos (id, email, nombre) de todos los usuarios
     * con rol soporte o admin activos.
     * Usado para enviarles emails de notificación.
     */
    public function getStaffUsersWithEmail(): array {
        $sql = "SELECT id, email, nombre FROM users 
                WHERE rol IN ('soporte', 'admin') AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
