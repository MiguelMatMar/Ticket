<?php
namespace App\Models;

use App\Core\Database;

class TokenModel {
    private $db;

    /**
     * Inicializa la conexión a la base de datos mediante el patrón Singleton.
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crea un nuevo registro de token de sesión en la base de datos, 
     * guardando el hash del token, el agente de usuario y una fecha de expiración a 30 días.
     */
    public function createToken($userId, $token) {
        $hash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, token_hash, user_agent, expires_at) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $hash, $userAgent, $expires]);
    }

    /**
     * Valida si un token proporcionado existe en la base de datos y sigue siendo vigente 
     * según su fecha de expiración; retorna el ID de usuario si es correcto.
     */
    public function validateToken($token) {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare("SELECT user_id FROM user_tokens WHERE token_hash = ? AND expires_at > NOW()");
        $stmt->execute([$hash]);
        return $stmt->fetchColumn();
    }

    /**
     * Elimina permanentemente un token de sesión de la base de datos utilizando su hash.
     */
    public function deleteToken($token) {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE token_hash = ?");
        return $stmt->execute([$hash]);
    }

    /**
     * Realiza una limpieza de mantenimiento eliminando todos los registros 
     * de la tabla de tokens cuya fecha de expiración ya ha pasado.
     */
    public function cleanExpiredTokens() {
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE expires_at < NOW()");
        return $stmt->execute();
    }
}