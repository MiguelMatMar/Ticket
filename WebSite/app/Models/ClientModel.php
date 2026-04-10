<?php
namespace App\Models;

use App\Core\Database;

class ClientModel {
    private $db;

    /**
     * Inicializa la conexión a la base de datos mediante el patrón Singleton.
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene toda la información almacenada en el perfil del usuario.
     */
    public function getUserData($userId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Devuelve estadísticas para el dashboard según el rol:
     * - cliente: solo sus tickets no cerrados
     * - admin / soporte: todos los tickets no cerrados
     */
    public function getDashboardStats($userId, $rol) {
        return [
            'tickets_count' => $this->countTickets($userId, $rol)
        ];
    }

    /**
     * Obtiene los últimos 5 tickets ordenados del más reciente al más antiguo.
     * - cliente: solo sus propios tickets (cualquier estado)
     * - admin / soporte: los 5 últimos de todos los usuarios, con nombre del creador
     */
    public function getRecentTickets($userId, $rol) {
        if ($rol === 'cliente') {
            $stmt = $this->db->prepare("
                SELECT id, asunto, status, fecha
                FROM tickets
                WHERE user_id = ?
                ORDER BY fecha DESC
                LIMIT 5
            ");
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT t.id, t.asunto, t.status, t.fecha,
                       u.nombre, u.apellidos
                FROM tickets t
                JOIN users u ON u.id = t.user_id
                ORDER BY t.fecha DESC
                LIMIT 5
            ");
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    /**
     * Verifica que la contraseña actual del usuario sea correcta.
     */
    public function verifyPassword($userId, $password) {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return password_verify($password, $result['password']);
    }

    /**
     * Actualiza la contraseña del usuario con hash seguro.
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Cuenta los tickets según el rol:
     * - cliente: solo los suyos no cerrados
     * - admin / soporte: todos los no cerrados
     */
    private function countTickets($userId, $rol) {
        if ($rol === 'cliente') {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM tickets
                WHERE user_id = ? AND status != 'closed'
            ");
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM tickets
                WHERE status != 'closed'
            ");
            $stmt->execute();
        }
        return $stmt->fetchColumn();
    }

    /**
     * Actualiza los datos de perfil del usuario, incluyendo
     * los campos de contacto y tipo de cuenta.
     */
    public function updateUserData($userId, $data) {
        $sql = "UPDATE users SET 
                    nombre          = ?,
                    apellidos       = ?,
                    email           = ?,
                    tipo            = ?,
                    empresa         = ?,
                    nif             = ?,
                    telefono        = ?,
                    telefono_movil  = ?,
                    whatsapp        = ?,
                    email_contacto  = ?,
                    direccion1      = ?,
                    direccion2      = ?,
                    ciudad          = ?,
                    provincia       = ?,
                    codigo_postal   = ?,
                    pais            = ?,
                    idioma          = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'],
            $data['email'],
            $data['tipo']           ?? 'persona',
            $data['empresa']        ?? '',
            $data['nif'],
            $data['telefono'],
            $data['telefono_movil'] ?? '',
            $data['whatsapp']       ?? '',
            $data['email_contacto'] ?? '',
            $data['direccion1'],
            $data['direccion2']     ?? '',
            $data['ciudad'],
            $data['provincia'],
            $data['codigo_postal'],
            $data['pais'],
            $data['idioma']         ?? 'es',
            $userId
        ]);
    }

    /**
     * Actualiza únicamente el rol de un usuario específico.
     */
    public function updateUserRole($userId, $role) {
        $allowedRoles = ['cliente', 'soporte', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE users SET rol = ? WHERE id = ?");
        return $stmt->execute([$role, $userId]);
    }
}