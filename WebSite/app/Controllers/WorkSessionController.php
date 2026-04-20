<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\WorkSessionModel;
use App\Models\TicketModel;

class WorkSessionController extends Controller {
    private $workModel;
    private $ticketModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        if (!in_array($_SESSION['user_role'], ['admin', 'soporte'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permiso']);
            exit;
        }
        $this->workModel   = new WorkSessionModel();
        $this->ticketModel = new TicketModel();
    }

    /**
     * POST /worksession/start
     * Inicia o reanuda trabajo. Bloquea solo si hay alguien en 'working'.
     */
    public function start() {
        header('Content-Type: application/json');
        $ticketId = (int) ($_POST['ticket_id'] ?? 0);
        $userId   = (int) $_SESSION['user_id'];

        if (!$ticketId) {
            echo json_encode(['success' => false, 'error' => 'ticket_id requerido']);
            exit;
        }

        $ticket = $this->ticketModel->getTicketByIdAdmin($ticketId);
        if (!$ticket) {
            echo json_encode(['success' => false, 'error' => 'Ticket no encontrado']);
            exit;
        }

        $ronda = (int) $ticket['ronda'];

        if ($this->workModel->isRondaFinished($ticketId, $ronda)) {
            echo json_encode(['success' => false, 'error' => 'El trabajo de esta ronda ya fue finalizado']);
            exit;
        }

        $working = $this->workModel->getWorkingSession($ticketId, $ronda);
        if ($working && (int) $working['user_id'] !== $userId) {
            echo json_encode(['success' => false, 'error' => 'Ya hay un técnico trabajando en este ticket']);
            exit;
        }

        $mySession = $this->workModel->getMySession($ticketId, $ronda, $userId);

        if ($mySession && $mySession['estado'] === 'paused') {
            $this->workModel->resumeSession((int) $mySession['id']);
            echo json_encode(['success' => true, 'session_id' => (int) $mySession['id'], 'estado' => 'working']);
            exit;
        }

        if ($mySession && $mySession['estado'] === 'working') {
            echo json_encode(['success' => true, 'session_id' => (int) $mySession['id'], 'estado' => 'working']);
            exit;
        }

        $sessionId = $this->workModel->startSession($ticketId, $userId, $ronda);
        echo json_encode(['success' => true, 'session_id' => $sessionId, 'estado' => 'working']);
        exit;
    }

    /**
     * POST /worksession/pause
     */
    public function pause() {
        header('Content-Type: application/json');
        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $ticketId  = (int) ($_POST['ticket_id'] ?? 0);
        $userId    = (int) $_SESSION['user_id'];

        if (!$sessionId || !$ticketId) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        $ticket    = $this->ticketModel->getTicketByIdAdmin($ticketId);
        $ronda     = (int) $ticket['ronda'];
        $mySession = $this->workModel->getMySession($ticketId, $ronda, $userId);

        if (!$mySession || (int) $mySession['id'] !== $sessionId || $mySession['estado'] !== 'working') {
            echo json_encode(['success' => false, 'error' => 'No puedes pausar esta sesión']);
            exit;
        }

        $this->workModel->pauseSession($sessionId);
        echo json_encode(['success' => true, 'estado' => 'paused']);
        exit;
    }

    /**
     * POST /worksession/finish
     * Finaliza la sesión y cierra también las pausadas de otros en esta ronda.
     */
    public function finish() {
        header('Content-Type: application/json');
        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $ticketId  = (int) ($_POST['ticket_id'] ?? 0);
        $userId    = (int) $_SESSION['user_id'];

        if (!$sessionId || !$ticketId) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        $ticket    = $this->ticketModel->getTicketByIdAdmin($ticketId);
        $ronda     = (int) $ticket['ronda'];
        $mySession = $this->workModel->getMySession($ticketId, $ronda, $userId);

        if (!$mySession || (int) $mySession['id'] !== $sessionId) {
            echo json_encode(['success' => false, 'error' => 'No puedes finalizar esta sesión']);
            exit;
        }

        // Pasa ticketId y ronda para que también cierre las pausadas de otros
        $this->workModel->finishSession($sessionId, $mySession['estado'], $ticketId, $ronda);
        echo json_encode(['success' => true, 'estado' => 'finished']);
        exit;
    }

    /**
     * GET /worksession/status?ticket_id=X
     * Devuelve el estado completo. Incluye elapsed_secs calculado en PHP
     * para evitar problemas de zona horaria en el JS.
     */
    public function status() {
        header('Content-Type: application/json');
        $ticketId = (int) ($_GET['ticket_id'] ?? 0);
        $userId   = (int) $_SESSION['user_id'];

        if (!$ticketId) {
            echo json_encode(['success' => false, 'error' => 'ticket_id requerido']);
            exit;
        }

        $ticket  = $this->ticketModel->getTicketByIdAdmin($ticketId);
        $ronda   = (int) $ticket['ronda'];

        $rondaFinished      = $this->workModel->isRondaFinished($ticketId, $ronda);
        $working            = $this->workModel->getWorkingSession($ticketId, $ronda);
        $mySession          = $this->workModel->getMySession($ticketId, $ronda, $userId);
        $totalRonda         = $this->workModel->getTotalSecondsByRonda($ticketId, $ronda);
        $someoneElseWorking = $working && ((int) $working['user_id'] !== $userId);

        // Bug 1 fix: calcular elapsed_secs en PHP para que el JS no tenga
        // que interpretar zonas horarias
        $elapsedSecs = 0;
        if ($mySession && $mySession['estado'] === 'working') {
            $elapsedSecs = $this->workModel->getElapsedSeconds((int) $mySession['id']);
        }

        echo json_encode([
            'success'              => true,
            'ronda_finished'       => $rondaFinished,
            'working_session'      => $working,
            'my_session'           => $mySession,
            'someone_else_working' => $someoneElseWorking,
            'total_ronda_secs'     => $totalRonda,
            'elapsed_secs'         => $elapsedSecs,  // segundos desde started_at calculados en servidor
            'ronda'                => $ronda,
        ]);
        exit;
    }

    /**
     * GET /worksession/history?ticket_id=X
     */
    public function history() {
        header('Content-Type: application/json');
        $ticketId = (int) ($_GET['ticket_id'] ?? 0);

        if (!$ticketId) {
            echo json_encode(['success' => false, 'error' => 'ticket_id requerido']);
            exit;
        }

        $sessions    = $this->workModel->getSessionsByTicket($ticketId);
        $totalGlobal = $this->workModel->getTotalSeconds($ticketId);

        echo json_encode([
            'success'      => true,
            'sessions'     => $sessions,
            'total_global' => $totalGlobal,
        ]);
        exit;
    }
}