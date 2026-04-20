<?php
namespace App\Models;

use App\Core\Database;

class TicketModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createTicket($userId, $asunto, $mensaje, $departamento, $prioridad) {
        $sql = "INSERT INTO tickets (user_id, asunto, mensaje, departamento, prioridad, status, fecha) 
                VALUES (:user_id, :asunto, :mensaje, :departamento, :prioridad, 'open', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'      => $userId,
            'asunto'       => $asunto,
            'mensaje'      => $mensaje,
            'departamento' => $departamento,
            'prioridad'    => $prioridad
        ]);
        return $this->db->lastInsertId();
    }
    public function getClients() {
        $sql = "SELECT id, nombre, apellidos, email
                FROM users
                WHERE rol = 'cliente'
                AND status = 1
                ORDER BY nombre ASC, apellidos ASC";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addResponse($ticketId, $userId, $mensaje) {
        $sql = "INSERT INTO ticket_responses (ticket_id, user_id, mensaje, fecha) 
                VALUES (:ticket_id, :user_id, :mensaje, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'ticket_id' => $ticketId,
            'user_id'   => $userId,
            'mensaje'   => $mensaje
        ]);

        $this->updateStatus($ticketId, 'customer-reply');

        return $this->db->lastInsertId();
    }

    public function addAttachment($ticketId, $fileName, $filePath, $fileType) {
        $sql = "INSERT INTO ticket_attachments (ticket_id, file_name, file_path, file_type) 
                VALUES (:ticket_id, :file_name, :file_path, :file_type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'ticket_id' => $ticketId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $fileType
        ]);
    }

    public function updateStatus($ticketId, $status) {
        $sql = "UPDATE tickets SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $ticketId
        ]);
    }

    public function updateLastActivity($ticketId) {
        $sql = "UPDATE tickets SET fecha = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $ticketId]);
    }

    public function getTicketsByUser($userId) {
        $sql = "SELECT * FROM tickets WHERE user_id = :user_id ORDER BY fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTicketWithAttachments($ticketId) {
        $sql = "SELECT t.*, a.file_name, a.file_path 
                FROM tickets t 
                LEFT JOIN ticket_attachments a ON t.id = a.ticket_id 
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $ticketId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAttachmentsByTicketId($ticketId) {
        $sql = "SELECT * FROM ticket_attachments WHERE ticket_id = :ticket_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTicketById($ticketId, $userId) {
        $sql = "SELECT t.*, u.nombre AS user_nombre 
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :id AND t.user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id'      => $ticketId,
            'user_id' => $userId
        ]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getResponsesByTicketId($ticketId) {
        $sql = "SELECT r.*, u.nombre AS usuario_nombre, u.rol 
                FROM ticket_responses r
                JOIN users u ON r.user_id = u.id
                WHERE r.ticket_id = :ticket_id
                ORDER BY r.fecha ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve el user_id del dueño de un ticket,
     * usado para crear notificaciones al responder.
     */
    public function getTicketOwner($ticketId) {
        $sql = "SELECT user_id FROM tickets WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $ticketId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Igual que getTicketById pero sin filtrar por user_id.
     * Usado por soporte y admin para acceder a cualquier ticket.
     */
    public function getTicketByIdAdmin($ticketId) {
        $sql = "SELECT t.*, u.nombre AS user_nombre, u.email AS user_email
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                WHERE t.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $ticketId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve todos los tickets del sistema con el nombre del cliente,
     * ordenados por fecha descendente. Usado por soporte y admin.
     */
    public function getAllTickets() {
        $sql = "SELECT t.*, u.nombre AS user_nombre
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                ORDER BY t.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve el conteo de tickets agrupados por estado.
     * Si se pasa userId solo cuenta los del usuario, si no cuenta todos.
     */
    public function getTicketStats($userId = null) {
        if ($userId) {
            $sql = "SELECT
                        SUM(status = 'open') AS abiertos,
                        SUM(status = 'answered') AS contestados,
                        SUM(status = 'customer-reply') AS respuesta_cliente,
                        SUM(status = 'closed') AS cerrados
                    FROM tickets WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
        } else {
            $sql = "SELECT
                        SUM(status = 'open') AS abiertos,
                        SUM(status = 'answered') AS contestados,
                        SUM(status = 'customer-reply') AS respuesta_cliente,
                        SUM(status = 'closed') AS cerrados
                    FROM tickets";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve todos los usuarios del sistema.
     * Usado únicamente por administradores.
     */
    public function getAllUsers() {
        $sql = "SELECT id, nombre, apellidos, email, empresa, telefono, rol, status, created_at
                FROM users
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Activa o desactiva un usuario cambiando su campo status.
     * Usado únicamente por administradores.
     */
    public function updateUserStatus($userId, $status) {
        $sql = "UPDATE users SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id'     => $userId
        ]);
    }

    /**
     * Obtiene los datos completos de un usuario antes de eliminarlo,
     * para guardarlos en eliminated_accounts.
     */
    public function getUserById($userId) {
        $sql = "SELECT id, nombre, apellidos, email, empresa, nif, telefono, rol
                FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Registra los datos del usuario eliminado en la tabla eliminated_accounts
     * para bloquear futuros registros con el mismo email.
     */
    public function registerEliminatedAccount($user, $eliminatedBy) {
        $sql = "INSERT INTO eliminated_accounts 
                    (email, nombre, apellidos, empresa, nif, telefono, rol, eliminated_by)
                VALUES 
                    (:email, :nombre, :apellidos, :empresa, :nif, :telefono, :rol, :eliminated_by)
                ON DUPLICATE KEY UPDATE
                    eliminated_at = NOW(),
                    eliminated_by = VALUES(eliminated_by)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'email'         => $user['email'],
            'nombre'        => $user['nombre'],
            'apellidos'     => $user['apellidos'] ?? '',
            'empresa'       => $user['empresa'] ?? '',
            'nif'           => $user['nif'] ?? '',
            'telefono'      => $user['telefono'] ?? '',
            'rol'           => $user['rol'],
            'eliminated_by' => $eliminatedBy
        ]);
    }

    /**
     * Elimina definitivamente un usuario de la base de datos.
     * Las FK con ON DELETE CASCADE eliminarán sus tickets, respuestas y notificaciones.
     */
    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    /**
     * Comprueba si un email está en la lista de cuentas eliminadas.
     * Usado en el registro para bloquear emails bloqueados.
     */
    public function isEmailEliminated($email) {
        $sql = "SELECT id FROM eliminated_accounts WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }
    /**
     * Incrementa en 1 la ronda de un ticket.
     * Se llama cuando un ticket se reabre.
     */
    public function incrementRonda(int $ticketId): bool {
        $sql = "UPDATE tickets SET ronda = ronda + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $ticketId]);
    }

}