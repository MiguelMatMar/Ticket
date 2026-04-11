<?php
namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    private array $config;

    public function __construct() {
        $cfg          = require __DIR__ . '/../../config.php';
        $this->config = $cfg['mail'];
    }

    /**
     * Crea y configura una instancia de PHPMailer lista para enviar.
     */
    private function buildMailer(): PHPMailer {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $this->config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->config['user'];
        $mail->Password   = $this->config['pass'];
        $mail->SMTPSecure = $this->config['secure'];
        $mail->Port       = (int) $this->config['port'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($this->config['from'], $this->config['from_name']);

        return $mail;
    }

    /**
     * Notifica a los admins/técnicos cuando un cliente abre un nuevo ticket.
     */
    public function sendNewTicketToStaff(
        array $staffEmails,
        int $ticketId,
        string $asunto,
        string $mensaje,
        string $clienteNombre,
        string $prioridad,
        string $departamento
    ): void {
        if (empty($staffEmails)) return;

        try {
            $mail = $this->buildMailer();

            foreach ($staffEmails as $staff) {
                $mail->addAddress($staff['email'], $staff['nombre'] ?? '');
            }

            $mail->isHTML(true);
            $mail->Subject = "[Ticket #{$ticketId}] Nuevo ticket: {$asunto}";
            $mail->Body    = $this->templateNewTicket($ticketId, $asunto, $mensaje, $clienteNombre, $prioridad, $departamento);
            $mail->AltBody = $this->plainTextNewTicket($ticketId, $asunto, $mensaje, $clienteNombre);

            $mail->send();

        } catch (Exception $e) {
            error_log("[EmailService] Error al notificar nuevo ticket #{$ticketId}: " . $e->getMessage());
        }
    }

    /**
     * Notifica al cliente cuando el staff añade una nueva respuesta a su ticket.
     */
    public function sendNewCommentToClient(
        string $clienteEmail,
        string $clienteNombre,
        int $ticketId,
        string $asunto,
        string $respuesta,
        string $staffNombre
    ): void {
        try {
            $mail = $this->buildMailer();
            $mail->addAddress($clienteEmail, $clienteNombre);

            $mail->isHTML(true);
            $mail->Subject = "[Ticket #{$ticketId}] Nueva respuesta: {$asunto}";
            $mail->Body    = $this->templateNewComment($ticketId, $asunto, $respuesta, $clienteNombre, $staffNombre);
            $mail->AltBody = $this->plainTextNewComment($ticketId, $asunto, $respuesta, $staffNombre);

            $mail->send();

        } catch (Exception $e) {
            error_log("[EmailService] Error al notificar comentario en ticket #{$ticketId}: " . $e->getMessage());
        }
    }

    /**
     * Notifica a los admins/técnicos cuando el cliente responde a un ticket.
     */
    public function sendClientReplyToStaff(
        array $staffEmails,
        int $ticketId,
        string $asunto,
        string $respuesta,
        string $clienteNombre
    ): void {
        if (empty($staffEmails)) return;

        try {
            $mail = $this->buildMailer();

            foreach ($staffEmails as $staff) {
                $mail->addAddress($staff['email'], $staff['nombre'] ?? '');
            }

            $mail->isHTML(true);
            $mail->Subject = "[Ticket #{$ticketId}] El cliente ha respondido: {$asunto}";
            $mail->Body    = $this->templateClientReply($ticketId, $asunto, $respuesta, $clienteNombre);
            $mail->AltBody = $this->plainTextClientReply($ticketId, $asunto, $respuesta, $clienteNombre);

            $mail->send();

        } catch (Exception $e) {
            error_log("[EmailService] Error al notificar respuesta del cliente en ticket #{$ticketId}: " . $e->getMessage());
        }
    }


    // ─── PLANTILLAS HTML ───────────────────────────────────────────────────────

    private function templateNewTicket(
        int $ticketId,
        string $asunto,
        string $mensaje,
        string $clienteNombre,
        string $prioridad,
        string $departamento
    ): string {
        $ticketUrl      = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";
        $prioridadBadge = $this->prioridadBadge($prioridad);

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
        <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <tr><td style="background:#004a87;padding:28px 32px;">
                  <p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">🎫 Nuevo Ticket Abierto</p>
                  <p style="margin:6px 0 0;color:#a8cbea;font-size:14px;">Sistema de soporte técnico</p>
                </td></tr>
                <tr><td style="padding:32px;">
                  <p style="margin:0 0 16px;font-size:15px;color:#333;">Se ha abierto un nuevo ticket que requiere atención:</p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:6px;padding:20px;margin-bottom:24px;">
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;width:130px;">Ticket</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;font-weight:bold;">#{$ticketId}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Cliente</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;">{$clienteNombre}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Asunto</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;font-weight:bold;">{$asunto}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Departamento</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;">{$departamento}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Prioridad</td>
                      <td style="padding:6px 0;">{$prioridadBadge}</td>
                    </tr>
                  </table>
                  <p style="margin:0 0 8px;font-size:14px;color:#666;font-weight:bold;">Mensaje del cliente:</p>
                  <div style="background:#f0f4f8;border-left:4px solid #004a87;padding:16px;border-radius:0 6px 6px 0;font-size:14px;color:#333;line-height:1.6;">{$mensaje}</div>
                  <div style="text-align:center;margin-top:28px;">
                    <a href="{$ticketUrl}" style="display:inline-block;background:#004a87;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:bold;">Ver ticket #{$ticketId}</a>
                  </div>
                </td></tr>
                <tr><td style="background:#f8f9fa;padding:20px 32px;border-top:1px solid #e9ecef;">
                  <p style="margin:0;font-size:12px;color:#999;text-align:center;">Este es un email automático del sistema de soporte. No respondas directamente a este correo.</p>
                </td></tr>
              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private function templateNewComment(
        int $ticketId,
        string $asunto,
        string $respuesta,
        string $clienteNombre,
        string $staffNombre
    ): string {
        $ticketUrl = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
        <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <tr><td style="background:#004a87;padding:28px 32px;">
              <p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">💬 Nueva respuesta en tu ticket</p>
              <p style="margin:6px 0 0;color:#a8cbea;font-size:14px;">Sistema de soporte técnico</p>
            </td></tr>
            <tr><td style="padding:32px;">
              <p style="margin:0 0 16px;font-size:15px;color:#333;">Hola <strong>{$clienteNombre}</strong>, el equipo de soporte ha respondido a tu ticket:</p>
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:6px;padding:20px;margin-bottom:24px;">
                <tr>
                  <td style="padding:6px 0;font-size:14px;color:#666;width:130px;">Ticket</td>
                  <td style="padding:6px 0;font-size:14px;color:#333;font-weight:bold;">#{$ticketId}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;font-size:14px;color:#666;">Asunto</td>
                  <td style="padding:6px 0;font-size:14px;color:#333;">{$asunto}</td>
                </tr>
                <tr>
                  <td style="padding:6px 0;font-size:14px;color:#666;">Respondido por</td>
                  <td style="padding:6px 0;font-size:14px;color:#333;">{$staffNombre}</td>
                </tr>
              </table>
              <p style="margin:0 0 8px;font-size:14px;color:#666;font-weight:bold;">Respuesta:</p>
              <div style="background:#f0f4f8;border-left:4px solid #004a87;padding:16px;border-radius:0 6px 6px 0;font-size:14px;color:#333;line-height:1.6;">{$respuesta}</div>
              <div style="text-align:center;margin-top:28px;">
                <a href="{$ticketUrl}" style="display:inline-block;background:#004a87;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:bold;">Ver mi ticket</a>
              </div>
            </td></tr>
            <tr><td style="background:#f8f9fa;padding:20px 32px;border-top:1px solid #e9ecef;">
              <p style="margin:0;font-size:12px;color:#999;text-align:center;">Este es un email automático. Para responder, accede a tu área de cliente.</p>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }

    private function templateClientReply(
        int $ticketId,
        string $asunto,
        string $respuesta,
        string $clienteNombre
    ): string {
        $ticketUrl = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
        <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <tr><td style="background:#1a6b3a;padding:28px 32px;">
                  <p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">↩️ El cliente ha respondido</p>
                  <p style="margin:6px 0 0;color:#a0d4b5;font-size:14px;">Sistema de soporte técnico</p>
                </td></tr>
                <tr><td style="padding:32px;">
                  <p style="margin:0 0 16px;font-size:15px;color:#333;"><strong>{$clienteNombre}</strong> ha añadido una respuesta al ticket:</p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:6px;padding:20px;margin-bottom:24px;">
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;width:130px;">Ticket</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;font-weight:bold;">#{$ticketId}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Asunto</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;">{$asunto}</td>
                    </tr>
                    <tr>
                      <td style="padding:6px 0;font-size:14px;color:#666;">Cliente</td>
                      <td style="padding:6px 0;font-size:14px;color:#333;">{$clienteNombre}</td>
                    </tr>
                  </table>
                  <p style="margin:0 0 8px;font-size:14px;color:#666;font-weight:bold;">Mensaje del cliente:</p>
                  <div style="background:#f0f8f4;border-left:4px solid #1a6b3a;padding:16px;border-radius:0 6px 6px 0;font-size:14px;color:#333;line-height:1.6;">{$respuesta}</div>
                  <div style="text-align:center;margin-top:28px;">
                    <a href="{$ticketUrl}" style="display:inline-block;background:#1a6b3a;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:bold;">Responder al ticket</a>
                  </div>
                </td></tr>
                <tr><td style="background:#f8f9fa;padding:20px 32px;border-top:1px solid #e9ecef;">
                  <p style="margin:0;font-size:12px;color:#999;text-align:center;">Este es un email automático del sistema de soporte. No respondas directamente a este correo.</p>
                </td></tr>
              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;
    }


    // ─── TEXTO PLANO (fallback) ────────────────────────────────────────────────

    private function plainTextNewTicket(int $ticketId, string $asunto, string $mensaje, string $clienteNombre): string {
        $url = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";
        return "Nuevo ticket #{$ticketId}\nCliente: {$clienteNombre}\nAsunto: {$asunto}\n\n{$mensaje}\n\nVer ticket: {$url}";
    }

    private function plainTextNewComment(int $ticketId, string $asunto, string $respuesta, string $staffNombre): string {
        $url = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";
        return "Nueva respuesta en ticket #{$ticketId}\nAsunto: {$asunto}\nRespondido por: {$staffNombre}\n\n{$respuesta}\n\nVer ticket: {$url}";
    }

    private function plainTextClientReply(int $ticketId, string $asunto, string $respuesta, string $clienteNombre): string {
        $url = $this->getBaseUrl() . "/support/ticket?ticketId={$ticketId}";
        return "El cliente ha respondido al ticket #{$ticketId}\nAsunto: {$asunto}\nCliente: {$clienteNombre}\n\n{$respuesta}\n\nVer ticket: {$url}";
    }


    // ─── UTILIDADES ────────────────────────────────────────────────────────────

    private function prioridadBadge(string $prioridad): string {
        $colores = [
            'baja'    => ['bg' => '#e8f5e9', 'text' => '#2e7d32'],
            'media'   => ['bg' => '#fff3e0', 'text' => '#e65100'],
            'alta'    => ['bg' => '#fce4ec', 'text' => '#b71c1c'],
            'urgente' => ['bg' => '#b71c1c', 'text' => '#ffffff'],
        ];
        $color = $colores[strtolower($prioridad)] ?? ['bg' => '#e0e0e0', 'text' => '#333'];
        return "<span style=\"background:{$color['bg']};color:{$color['text']};padding:3px 10px;border-radius:12px;font-size:13px;font-weight:bold;\">" . ucfirst($prioridad) . "</span>";
    }

    private function getBaseUrl(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
}