<?php
namespace App\Models;

use App\Core\Database;

class NotificationModel {
    private $db;

    /**
     * Inicializa la conexión a la base de datos utilizando el patrón Singleton.
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Recupera todas las notificaciones no leídas de un usuario,
     * ordenadas de la más reciente a la más antigua.
     */
    public function getUnreadByUser($userId) {
        $sql = "SELECT n.id, n.ticket_id, n.mensaje, n.created_at
                FROM notifications n
                WHERE n.user_id = :user_id AND n.is_read = 0
                ORDER BY n.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Marca una notificación concreta como leída,
     * verificando que pertenezca al usuario que hace la petición.
     */
    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 
                WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'      => $notificationId,
            'user_id' => $userId
        ]);
    }

    /**
     * Inserta una nueva notificación para un usuario sobre un ticket concreto.
     */
    public function create($userId, $ticketId, $mensaje) {
        $sql = "INSERT INTO notifications (user_id, ticket_id, mensaje) 
                VALUES (:user_id, :ticket_id, :mensaje)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id'   => $userId,
            'ticket_id' => $ticketId,
            'mensaje'   => $mensaje
        ]);
    }

    /**
     * Devuelve los IDs de todos los usuarios con rol soporte o admin activos.
     * Usado para notificarles cuando un cliente abre o responde un ticket.
     */
    public function getStaffUsers() {
        $sql = "SELECT id FROM users 
                WHERE rol IN ('soporte', 'admin') AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Inserta notificaciones para múltiples usuarios a la vez.
     * Usado para notificar a todo el equipo de soporte/admin de una sola vez.
     */
    public function createBulk(array $userIds, $ticketId, $mensaje) {
        if (empty($userIds)) return;

        $sql  = "INSERT INTO notifications (user_id, ticket_id, mensaje) VALUES (:user_id, :ticket_id, :mensaje)";
        $stmt = $this->db->prepare($sql);

        foreach ($userIds as $userId) {
            $stmt->execute([
                'user_id'   => $userId,
                'ticket_id' => $ticketId,
                'mensaje'   => $mensaje
            ]);
        }
    }
}