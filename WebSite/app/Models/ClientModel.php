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
     * Devuelve el número de tickets abiertos del usuario para el dashboard.
     */
    public function getDashboardStats($userId) {
        return [
            'tickets_count' => $this->countTickets($userId)
        ];
    }

    /**
     * Obtiene los últimos 15 tickets de soporte creados por el usuario que no estén finalizados,
     * ordenados cronológicamente de forma descendente.
     */
    public function getRecentTickets($userId) {
        $stmt = $this->db->prepare("SELECT id, asunto, status, fecha FROM tickets WHERE user_id = ? AND (status = 'open' OR status = 'answered' OR status = 'customer-reply') ORDER BY fecha DESC LIMIT 15");
        $stmt->execute([$userId]);
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
     * Método auxiliar que cuenta cuántos tickets tiene el usuario
     * que aún no han sido cerrados.
     */
    private function countTickets($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status != 'closed'");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    /**
     * Actualiza los datos de perfil del usuario.
     */
    public function updateUserData($userId, $data) {
        $sql = "UPDATE users SET 
                nombre = ?, 
                apellidos = ?, 
                email = ?, 
                empresa = ?, 
                telefono = ?, 
                nif = ?, 
                direccion1 = ?, 
                direccion2 = ?, 
                ciudad = ?, 
                provincia = ?, 
                codigo_postal = ?, 
                pais = ?, 
                idioma = ? 
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'],
            $data['email'],
            $data['empresa'],
            $data['telefono'],
            $data['nif'],
            $data['direccion1'],
            $data['direccion2'],
            $data['ciudad'],
            $data['provincia'],
            $data['codigo_postal'],
            $data['pais'],
            $data['idioma'],
            $userId
        ]);
    }
    /**
     * Actualiza únicamente el rol de un usuario específico.
     */
    public function updateUserRole($userId, $role) {
        // Validamos que el rol sea uno de los permitidos
        $allowedRoles = ['cliente', 'soporte', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE users SET rol = ? WHERE id = ?");
        return $stmt->execute([$role, $userId]);
    }
}