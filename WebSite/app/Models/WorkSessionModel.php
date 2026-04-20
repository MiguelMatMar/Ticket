<?php
namespace App\Models;

use App\Core\Database;

class WorkSessionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ─── CONSULTAS DE ESTADO ──────────────────────────────────────────────────

    public function getWorkingSession(int $ticketId, int $ronda): ?array {
        $sql = "SELECT ws.*, u.nombre, u.apellidos
                FROM ticket_work_sessions ws
                JOIN users u ON ws.user_id = u.id
                WHERE ws.ticket_id = :ticket_id
                  AND ws.ronda = :ronda
                  AND ws.estado = 'working'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'ronda' => $ronda]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getMySession(int $ticketId, int $ronda, int $userId): ?array {
        $sql = "SELECT ws.*, u.nombre, u.apellidos
                FROM ticket_work_sessions ws
                JOIN users u ON ws.user_id = u.id
                WHERE ws.ticket_id = :ticket_id
                  AND ws.ronda = :ronda
                  AND ws.user_id = :user_id
                  AND ws.estado IN ('working', 'paused')
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'ronda' => $ronda, 'user_id' => $userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function isRondaFinished(int $ticketId, int $ronda): bool {
        $sql = "SELECT COUNT(*) FROM ticket_work_sessions
                WHERE ticket_id = :ticket_id AND ronda = :ronda AND estado = 'finished'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'ronda' => $ronda]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getSessionsByTicket(int $ticketId): array {
        $sql = "SELECT ws.*, u.nombre, u.apellidos
                FROM ticket_work_sessions ws
                JOIN users u ON ws.user_id = u.id
                WHERE ws.ticket_id = :ticket_id
                ORDER BY ws.ronda ASC, ws.started_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTotalSeconds(int $ticketId): int {
        $sql = "SELECT COALESCE(SUM(total_segundos), 0)
                FROM ticket_work_sessions
                WHERE ticket_id = :ticket_id AND estado = 'finished'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        return (int) $stmt->fetchColumn();
    }

    public function getTotalSecondsByRonda(int $ticketId, int $ronda): int {
        $sql = "SELECT COALESCE(SUM(total_segundos), 0)
                FROM ticket_work_sessions
                WHERE ticket_id = :ticket_id AND ronda = :ronda";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'ronda' => $ronda]);
        $acumulado = (int) $stmt->fetchColumn();

        $sql = "SELECT COALESCE(TIMESTAMPDIFF(SECOND, started_at, NOW()), 0)
                FROM ticket_work_sessions
                WHERE ticket_id = :ticket_id AND ronda = :ronda AND estado = 'working'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'ronda' => $ronda]);
        $enCurso = (int) $stmt->fetchColumn();

        return $acumulado + $enCurso;
    }

    /**
     * Devuelve los segundos transcurridos desde started_at de una sesión 'working'.
     * Calculado en PHP/MySQL para evitar problemas de zona horaria en el JS.
     */
    public function getElapsedSeconds(int $sessionId): int {
        $sql = "SELECT TIMESTAMPDIFF(SECOND, started_at, NOW())
                FROM ticket_work_sessions
                WHERE id = :id AND estado = 'working'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $sessionId]);
        return (int) $stmt->fetchColumn();
    }

    // ─── ACCIONES ─────────────────────────────────────────────────────────────

    public function startSession(int $ticketId, int $userId, int $ronda): int {
        $sql = "INSERT INTO ticket_work_sessions (ticket_id, user_id, ronda, started_at, estado)
                VALUES (:ticket_id, :user_id, :ronda, NOW(), 'working')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId, 'user_id' => $userId, 'ronda' => $ronda]);
        return (int) $this->db->lastInsertId();
    }

    public function pauseSession(int $sessionId): bool {
        $sql = "UPDATE ticket_work_sessions
                SET estado         = 'paused',
                    paused_at      = NOW(),
                    total_segundos = total_segundos + TIMESTAMPDIFF(SECOND, started_at, NOW())
                WHERE id = :id AND estado = 'working'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $sessionId]);
    }

    public function resumeSession(int $sessionId): bool {
        $sql = "UPDATE ticket_work_sessions
                SET estado     = 'working',
                    started_at = NOW(),
                    paused_at  = NULL
                WHERE id = :id AND estado = 'paused'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $sessionId]);
    }

    /**
     * Finaliza la sesión del usuario Y cierra todas las sesiones pausadas
     * de la misma ronda (las de otros técnicos que se quedaron en pausa).
     * Bug 2: cuando B finaliza, la sesión pausada de A también debe cerrarse.
     */
    public function finishSession(int $sessionId, string $estado, int $ticketId, int $ronda): bool {
        // 1. Finalizar la sesión propia
        if ($estado === 'working') {
            $sql = "UPDATE ticket_work_sessions
                    SET estado         = 'finished',
                        finished_at    = NOW(),
                        total_segundos = total_segundos + TIMESTAMPDIFF(SECOND, started_at, NOW())
                    WHERE id = :id";
        } else {
            $sql = "UPDATE ticket_work_sessions
                    SET estado      = 'finished',
                        finished_at = NOW()
                    WHERE id = :id";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $sessionId]);

        // 2. Cerrar también cualquier sesión pausada de otros técnicos en esta ronda
        $sql = "UPDATE ticket_work_sessions
                SET estado      = 'finished',
                    finished_at = NOW()
                WHERE ticket_id = :ticket_id
                  AND ronda     = :ronda
                  AND estado    = 'paused'
                  AND id        != :session_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'ticket_id'  => $ticketId,
            'ronda'      => $ronda,
            'session_id' => $sessionId,
        ]);
    }

    public function forceFinishActiveSessions(int $ticketId): void {
        $sql = "UPDATE ticket_work_sessions
                SET estado         = 'finished',
                    finished_at    = NOW(),
                    total_segundos = total_segundos + TIMESTAMPDIFF(SECOND, started_at, NOW())
                WHERE ticket_id = :ticket_id AND estado = 'working'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);

        $sql = "UPDATE ticket_work_sessions
                SET estado = 'finished', finished_at = NOW()
                WHERE ticket_id = :ticket_id AND estado = 'paused'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
    }
}