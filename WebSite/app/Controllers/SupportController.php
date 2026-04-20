<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ClientModel;
use App\Models\TicketModel;
use App\Models\NotificationModel;
use App\Models\EmailService;
use App\Models\WorkSessionModel;

class SupportController extends Controller {
    private $clientModel;
    private $ticketModel;
    private $notificationModel;
    private $emailService;
    private $workSessionModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/index');
            exit;
        }
        $this->clientModel       = new ClientModel();
        $this->ticketModel       = new TicketModel();
        $this->notificationModel = new NotificationModel();
        $this->emailService      = new EmailService();
        $this->workSessionModel = new WorkSessionModel();
    }

    public function faq() {
        $userId   = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $data = [
            'usuario'       => $this->clientModel->getUserData($userId),
            'tickets_lista' => $this->clientModel->getRecentTickets($userId, $userRole),
            'title'         => "FAQ"
        ];
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/support/faq.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function open_ticket() {
        $userId       = $_SESSION['user_id'];
        $userRole     = $_SESSION['user_role'];
        $optionTicket = $_GET['dep'] ?? 'contacto';

        $data = [
            'usuario'       => $this->clientModel->getUserData($userId),
            'tickets_lista' => $this->clientModel->getRecentTickets($userId, $userRole),
            'title'         => "Abrir Ticket",
            'optionTicket'  => $optionTicket,
            'clientes'      => in_array($userRole, ['admin', 'soporte'])
                                    ? $this->ticketModel->getClients()
                                    : []
        ];

        extract($data);
        ob_start();
        require __DIR__ . '/../Views/support/open_ticket.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function store_ticket() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/support/open_ticket');
            return;
        }

        $sessionUserId = $_SESSION['user_id'] ?? null;
        $userRole      = $_SESSION['user_role'] ?? 'cliente';

        if (!$sessionUserId) {
            $this->redirect('/auth/index');
            return;
        }

        if (in_array($userRole, ['admin', 'soporte']) && !empty($_POST['target_user_id'])) {
            $ticketOwnerId = (int) $_POST['target_user_id'];
        } else {
            $ticketOwnerId = $sessionUserId;
        }

        $asunto       = trim($_POST['affairUser'] ?? '');
        $mensaje      = trim($_POST['messageUser'] ?? '');
        $departamento = $_POST['departmentUser'];
        $prioridad    = $_POST['priority'];

        $ticketId = $this->ticketModel->createTicket(
            $ticketOwnerId,
            $asunto,
            $mensaje,
            $departamento,
            $prioridad
        );

        // ── Archivos adjuntos ─────────────────────────────────────────────────
        if (isset($_FILES['fileUsers']) && is_array($_FILES['fileUsers']['name'])) {
            $uploadDir = __DIR__ . '/../../storage/tickets/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $totalArchivos = count($_FILES['fileUsers']['name']);
            for ($i = 0; $i < $totalArchivos; $i++) {
                if ($_FILES['fileUsers']['error'][$i] === UPLOAD_ERR_OK) {
                    $originalName = $_FILES['fileUsers']['name'][$i];
                    $fileType     = $_FILES['fileUsers']['type'][$i];
                    $storedName   = uniqid('tkt_', true) . '_' . basename($originalName);
                    $targetPath   = $uploadDir . $storedName;

                    if (move_uploaded_file($_FILES['fileUsers']['tmp_name'][$i], $targetPath)) {
                        $this->ticketModel->addAttachment($ticketId, $originalName, $storedName, $fileType);
                    }
                }
            }
        }

        // ── Notificaciones internas + Email ───────────────────────────────────
        if (in_array($userRole, ['admin', 'soporte'])) {
            if ($ticketOwnerId !== $sessionUserId) {
                // Notificación interna al cliente
                $this->notificationModel->create(
                    $ticketOwnerId,
                    $ticketId,
                    "Se ha abierto el ticket #{$ticketId}: \"{$asunto}\" en tu nombre."
                );

                // Email al cliente avisando de que se le ha creado un ticket
                $clienteData   = $this->clientModel->getUserData($ticketOwnerId);
                $clienteNombre = trim(($clienteData['nombre'] ?? '') . ' ' . ($clienteData['apellidos'] ?? ''));
                if (!empty($clienteData['email'])) {
                    $this->emailService->sendNewCommentToClient(
                        $clienteData['email'],
                        $clienteNombre,
                        $ticketId,
                        $asunto,
                        $mensaje,
                        'Soporte Técnico'
                    );
                }

                // Email a admins/técnicos avisando de que se ha abierto un ticket
                $staffData = $this->notificationModel->getStaffUsersWithEmail();
                $staffNombre = trim(($this->clientModel->getUserData($sessionUserId)['nombre'] ?? '') . ' ' . ($this->clientModel->getUserData($sessionUserId)['apellidos'] ?? ''));
                $this->emailService->sendNewTicketToStaff(
                    $staffData,
                    $ticketId,
                    $asunto,
                    $mensaje,
                    $clienteNombre . ' (creado por ' . $staffNombre . ')',
                    $prioridad,
                    $departamento
                );
            }
        } else {
            // Cliente creó su propio ticket: notificación interna + email al staff
            $staffIds = $this->notificationModel->getStaffUsers();
            if (!empty($staffIds)) {
                $this->notificationModel->createBulk(
                    $staffIds,
                    $ticketId,
                    "Nuevo ticket #{$ticketId}: \"{$asunto}\""
                );
            }

            $staffData     = $this->notificationModel->getStaffUsersWithEmail();
            $clienteData   = $this->clientModel->getUserData($sessionUserId);
            $clienteNombre = trim(($clienteData['nombre'] ?? '') . ' ' . ($clienteData['apellidos'] ?? ''));

            $this->emailService->sendNewTicketToStaff(
                $staffData,
                $ticketId,
                $asunto,
                $mensaje,
                $clienteNombre,
                $prioridad,
                $departamento
            );
        }

        // ── Respuesta con SweetAlert ──────────────────────────────────────────
        $data = [
            'usuario'     => $this->clientModel->getUserData($sessionUserId),
            'title'       => "Ticket Enviado",
            'redirect_to' => '/support/tickets'
        ];
        extract($data);

        ob_start();
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Ticket #<?= $ticketId ?> Enviado!',
                    text: 'La consulta se ha registrado correctamente.',
                    icon: 'success',
                    confirmButtonColor: '#004a87',
                    confirmButtonText: 'Ver tickets',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed || result.dismiss) {
                        window.location.href = "<?= $redirect_to ?>";
                    }
                });
            });
        </script>
        <div style="height: 60vh; display: flex; align-items: center; justify-content: center;">
            <p>Procesando envío... por favor espera.</p>
        </div>
        <?php
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
        exit;
    }

    public function tickets() {
        $userId   = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $usuario  = $this->clientModel->getUserData($userId);

        if (in_array($userRole, ['soporte', 'admin'])) {
            $tickets_lista = $this->ticketModel->getAllTickets();
            $stats         = $this->ticketModel->getTicketStats();
        } else {
            $tickets_lista = $this->ticketModel->getTicketsByUser($userId);
            $stats         = $this->ticketModel->getTicketStats($userId);
        }

        $data = [
            'usuario'       => $usuario,
            'tickets_lista' => $tickets_lista,
            'stats'         => $stats,
            'userRole'      => $userRole,
            'title'         => "Tickets"
        ];
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/support/tickets_list.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function ticket() {
        if (!isset($_GET['ticketId'])) {
            $this->redirect('/support/tickets');
            return;
        }

        $ticketId = $_GET['ticketId'];
        $userId   = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $usuario  = $this->clientModel->getUserData($userId);

        $ticket = in_array($userRole, ['soporte', 'admin'])
            ? $this->ticketModel->getTicketByIdAdmin($ticketId)
            : $this->ticketModel->getTicketById($ticketId, $userId);

        if (!$ticket) {
            $this->redirect('/support/tickets');
            return;
        }

        $data = [
            'usuario'    => $usuario,
            'ticket'     => $ticket,
            'respuestas' => $this->ticketModel->getResponsesByTicketId($ticketId),
            'adjuntos'   => $this->ticketModel->getAttachmentsByTicketId($ticketId),
            'ticketId'   => $ticketId,
            'userRole'   => $userRole,
            'title'      => "Ticket #" . $ticketId . " - " . $ticket['asunto']
        ];
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/support/ticket.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function download_file() {
        $fileName  = $_GET['file'] ?? '';
        $uploadDir = realpath(__DIR__ . '/../../storage/tickets/');
        $filePath  = realpath($uploadDir . '/' . $fileName);

        if ($filePath && file_exists($filePath) && strpos($filePath, $uploadDir) === 0) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo "El archivo no existe o no tienes permiso para acceder a él.";
        }
    }

    public function option_tickets() {
        $userId = $_SESSION['user_id'];
        $data = [
            'usuario' => $this->clientModel->getUserData($userId),
            'title'   => "Seleccionar Departamento"
        ];
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/support/option_tickets.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function store_response() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/support/tickets');
            return;
        }

        $userId   = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $ticketId = $_POST['ticket_id'];
        $mensaje  = $_POST['mensaje'];

        $this->ticketModel->addResponse($ticketId, $userId, $mensaje);

        if (in_array($userRole, ['soporte', 'admin'])) {
            $this->ticketModel->updateStatus($ticketId, 'answered');
        } else {
            $this->ticketModel->updateStatus($ticketId, 'customer-reply');
        }

        // ── Archivos adjuntos ─────────────────────────────────────────────────
        if (isset($_FILES['fileUsers']) && is_array($_FILES['fileUsers']['name'])) {
            $uploadDir = __DIR__ . '/../../storage/tickets/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $totalArchivos = count($_FILES['fileUsers']['name']);
            for ($i = 0; $i < $totalArchivos; $i++) {
                if ($_FILES['fileUsers']['error'][$i] === UPLOAD_ERR_OK) {
                    $originalName = $_FILES['fileUsers']['name'][$i];
                    $fileType     = $_FILES['fileUsers']['type'][$i];
                    $storedName   = uniqid('tkt_', true) . '_' . basename($originalName);
                    $targetPath   = $uploadDir . $storedName;
                    if (move_uploaded_file($_FILES['fileUsers']['tmp_name'][$i], $targetPath)) {
                        $this->ticketModel->addAttachment($ticketId, $originalName, $storedName, $fileType);
                    }
                }
            }
        }

        $this->ticketModel->updateLastActivity($ticketId);

        // ── Notificaciones internas + Email ───────────────────────────────────
        if (in_array($userRole, ['soporte', 'admin'])) {
            // Staff responde: notificación interna + email al cliente
            $ticket = $this->ticketModel->getTicketOwner($ticketId);
            if ($ticket) {
                $this->notificationModel->create(
                    $ticket['user_id'],
                    $ticketId,
                    "Tu ticket #{$ticketId} tiene una nueva respuesta."
                );

                $ticketData  = $this->ticketModel->getTicketByIdAdmin($ticketId);
                $staffData   = $this->clientModel->getUserData($userId);
                $staffNombre = trim(($staffData['nombre'] ?? '') . ' ' . ($staffData['apellidos'] ?? ''));

                if ($ticketData && !empty($ticketData['user_email'])) {
                    $clienteNombre = trim($ticketData['user_nombre'] ?? '');

                    $this->emailService->sendNewCommentToClient(
                        $ticketData['user_email'],
                        $clienteNombre,
                        (int) $ticketId,
                        $ticketData['asunto'],
                        $mensaje,
                        $staffNombre
                    );
                }
            }
        } else {
            // Cliente responde: notificación interna + email al staff
            $staffIds = $this->notificationModel->getStaffUsers();
            $this->notificationModel->createBulk(
                $staffIds,
                $ticketId,
                "El cliente ha respondido al ticket #{$ticketId}."
            );

            $staffEmailData = $this->notificationModel->getStaffUsersWithEmail();
            $clienteData    = $this->clientModel->getUserData($userId);
            $clienteNombre  = trim(($clienteData['nombre'] ?? '') . ' ' . ($clienteData['apellidos'] ?? ''));
            $ticketData     = $this->ticketModel->getTicketByIdAdmin($ticketId);

            if ($ticketData) {
                $this->emailService->sendClientReplyToStaff(
                    $staffEmailData,
                    (int) $ticketId,
                    $ticketData['asunto'],
                    $mensaje,
                    $clienteNombre
                );
            }
        }

        $this->redirect('/support/ticket?ticketId=' . $ticketId);
    }

    public function close_ticket() {
        $ticketId = $_GET['ticketId'] ?? null;
        $userId   = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        if ($ticketId) {
            if (!in_array($userRole, ['soporte', 'admin'])) {
                $ticket = $this->ticketModel->getTicketById($ticketId, $userId);
                if (!$ticket) {
                    $this->redirect('/support/tickets');
                    return;
                }
            }
            $this->ticketModel->updateStatus($ticketId, 'closed');
            $this->ticketModel->updateLastActivity($ticketId);
        }

        $this->redirect('/support/ticket?ticketId=' . $ticketId);
    }

    public function reopen_ticket() {
        $ticketId = $_GET['ticketId'] ?? null;
        $userRole = $_SESSION['user_role'];

        if (!in_array($userRole, ['soporte', 'admin'])) {
            $this->redirect('/support/tickets');
            return;
        }

        if ($ticketId) {
            // 1. Forzar cierre de cualquier sesión de trabajo activa que hubiera quedado abierta
            $this->workSessionModel->forceFinishActiveSessions((int) $ticketId);

            // 2. Incrementar la ronda del ticket
            $this->ticketModel->incrementRonda((int) $ticketId);

            // 3. Reabrir el ticket
            $this->ticketModel->updateStatus($ticketId, 'open');
            $this->ticketModel->updateLastActivity($ticketId);
        }

        $this->redirect('/support/ticket?ticketId=' . $ticketId);
    }

    public function users() {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/support/tickets');
            return;
        }

        $userId = $_SESSION['user_id'];
        $data = [
            'usuario'    => $this->clientModel->getUserData($userId),
            'users_list' => $this->ticketModel->getAllUsers(),
            'title'      => "Gestión de Usuarios"
        ];
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/admin/users.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout/main.php';
    }

    public function toggle_user_status() {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/support/tickets');
            return;
        }

        $targetUserId = $_GET['userId'] ?? null;
        $newStatus    = $_GET['status'] ?? null;

        if ($targetUserId && in_array($newStatus, ['0', '1'])) {
            $this->ticketModel->updateUserStatus($targetUserId, $newStatus);
        }

        $this->redirect('/support/users');
    }

    public function delete_user() {
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/support/tickets');
            return;
        }

        $targetUserId = $_GET['userId'] ?? null;
        $adminId      = $_SESSION['user_id'];

        if (!$targetUserId || $targetUserId == $adminId) {
            $this->redirect('/support/users');
            return;
        }

        $user = $this->ticketModel->getUserById($targetUserId);

        if (!$user) {
            $this->redirect('/support/users');
            return;
        }

        $this->ticketModel->registerEliminatedAccount($user, $adminId);
        $this->ticketModel->deleteUser($targetUserId);

        $this->redirect('/support/users');
    }

    public function update_user_role() {
        $userId = $_GET['userId'] ?? null;
        $role   = $_GET['role']   ?? null;

        if ($userId && $role) {
            $success = $this->clientModel->updateUserRole($userId, $role);
            if ($success) {
                header('Location: /support/users?success=role');
            } else {
                header('Location: /support/users?error=db');
            }
        } else {
            header('Location: /support/users?error=missing');
        }
        exit;
    }
}