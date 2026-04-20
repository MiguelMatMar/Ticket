<div class="content-ticket">
    <div class="header-main">
        <h1>
            Ticket #<?= htmlspecialchars($ticket['id']) ?>
            <?php if ((int)$ticket['ronda'] > 1): ?>
                - <?= (int)$ticket['ronda'] ?>
            <?php endif; ?>
            - <?= htmlspecialchars($ticket['asunto']) ?>
        </h1>
    </div>
    <div class="breadcrumb">
        <p>Administración > 
            <?= (isset($userRole) && in_array($userRole, ['soporte', 'admin'])) ? 'Panel de Soporte' : 'Área Cliente' ?> 
            > Tickets > Ver Ticket
        </p>
    </div>

    <div class="ticketContent">
        <div class="body-ticket">
            <div class="main-content">
                <div class="bodyInfo">

                    <?php if ($ticket['status'] !== 'closed'): ?>
                        <button class="responseTicketBtn" id="btnOpenReply">
                            <i class="fa-solid fa-pencil white"></i> RESPONDER
                        </button>

                        <div id="replyFormContainer" style="display: none;">
                            <div class="contentForm">
                                <h2>ENVIAR RESPUESTA</h2>
                                
                                <form action="/support/store_response" method="POST" enctype="multipart/form-data" id="formReplyTicket">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" value="<?= htmlspecialchars($usuario['nombre']) ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label> 
                                            <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>    
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="replyMessage">Mensaje</label>
                                        <textarea name="mensaje" id="replyMessage" required></textarea>
                                    </div>

                                    <div class="form-group" id="replyFilesContainer">
                                        <label>Adjuntos</label>
                                        <div id="inputsAdjuntos">
                                            <input type="file" name="fileUsers[]" class="file-input">
                                        </div>
                                        <button type="button" id="addFileReply"> + AÑADIR MÁS </button>
                                    </div>

                                    <div class="form-buttons">
                                        <button type="submit" class="btn-submit">Enviar Respuesta</button>
                                        <button type="button" class="btn-cancel" id="btnCloseReply">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert-closed">
                            <i class="fa-solid fa-lock"></i> Este ticket está cerrado. No se permiten más respuestas.
                        </div>

                        <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                            <button class="reopenTicketBtn" onclick="window.location.href='/support/reopen_ticket?ticketId=<?= $ticket['id'] ?>'">
                                <i class="fa-solid fa-lock-open"></i> REABRIR TICKET
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="responseTicket initial-msg">
                        <div class="headerResponse initial-header">
                            <div class="headerResponseLeft">
                                <div>
                                    <i class="fa-solid fa-user-pen"></i>
                                    <p class="responseName"><?= htmlspecialchars($ticket['user_nombre']) ?> <small>(Creador)</small></p>
                                    <p class="responseRole">Mensaje Inicial</p>
                                </div>
                            </div>
                            <div class="headerResponseRight">
                                <p class="timeComment"><?= date('d/m/Y (H:i)', strtotime($ticket['fecha'])) ?></p>
                            </div>
                        </div>
                        <div class="textResponse initial-body">
                            <?= nl2br($ticket['mensaje']) ?>
                        </div>
                    </div>

                    <h3 class="history-title">HISTORIAL DE CONVERSACIÓN</h3>

                    <?php if (!empty($respuestas)): ?>
                        <?php foreach ($respuestas as $res): ?>
                        <div class="responseTicket <?= in_array($res['rol'], ['soporte', 'admin']) ? 'response-staff' : 'response-client' ?>">
                            <div class="headerResponse">
                                <div class="headerResponseLeft">
                                    <div>
                                        <i class="fa-solid <?= in_array($res['rol'], ['soporte', 'admin']) ? 'fa-headset' : 'fa-user' ?>"></i>
                                        <p class="responseName"><?= htmlspecialchars($res['usuario_nombre']) ?></p>
                                        <p class="responseRole"><?= ucfirst(htmlspecialchars($res['rol'])) ?></p>
                                    </div>
                                </div>
                                <div class="headerResponseRight">
                                    <p class="timeComment"><?= date('d/m/Y (H:i)', strtotime($res['fecha'])) ?></p>
                                </div>
                            </div>
                            <div class="textResponse">
                                <?= $res['mensaje'] ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="right-boxes">
                <div class="info-ticket">
                    <div class="headerInfo">
                        <i class="fa-solid fa-ticket"></i>
                        <p>INFORMACIÓN DEL TICKET</p>
                    </div>
                    <div class="mainInfo">
                        <p>Requestor</p>
                        <p><strong><?= htmlspecialchars($ticket['user_nombre']) ?></strong></p>
                    </div>
                    <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                        <div class="mainInfo">
                            <p>Email cliente</p>
                            <p><?= htmlspecialchars($ticket['user_email']) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="mainInfo">
                        <p>Departamento</p>
                        <p><?= ucfirst(htmlspecialchars($ticket['departamento'])) ?></p>
                    </div>
                    <div class="mainInfo">
                        <p>Enviado</p>
                        <p><?= date('d/m/Y (H:i)', strtotime($ticket['fecha'])) ?></p>
                    </div>
                    <div class="mainInfo">
                        <p>Estado / Prioridad</p>
                        <p>
                            <span class="status-text status-<?= $ticket['status'] ?>">
                                <?php 
                                    echo ($ticket['status'] == 'open') ? 'Abierto' : 
                                         (($ticket['status'] == 'answered') ? 'Respondido' : 
                                         (($ticket['status'] == 'customer-reply') ? 'Respuesta Cliente' : 'Cerrado')); 
                                ?>
                            </span>
                            / <?= ucfirst(htmlspecialchars($ticket['prioridad'])) ?>
                        </p>
                    </div>
                    <div class="footerInfoTicket">
                        <?php if ($ticket['status'] !== 'closed'): ?>
                            <button class="closeTicketBtn" onclick="window.location.href='/support/close_ticket?ticketId=<?= $ticket['id'] ?>'">
                                <i class="fa-solid fa-lock white"></i> CERRAR TICKET
                            </button>
                        <?php else: ?>
                            <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                                <button class="reopenTicketBtn" onclick="window.location.href='/support/reopen_ticket?ticketId=<?= $ticket['id'] ?>'">
                                    <i class="fa-solid fa-lock-open"></i> REABRIR TICKET
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($ticket['status'] !== 'closed'): ?>
                            <button class="responseTickeInfotBtn" onclick="scrollToResponse()">
                                <i class="fa-solid fa-pencil white"></i> RESPONDER
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-ticket">
                    <div class="headerInfo">
                        <i class="fa-solid fa-paperclip"></i>
                        <p>ARCHIVOS ADJUNTOS</p>
                    </div>
                    <div class="bodyInfo">
                        <?php if (!empty($adjuntos)): ?>
                            <?php foreach ($adjuntos as $file): ?>
                                <div class="mainInfo">
                                    <a href="/support/download_file?file=<?= urlencode($file['file_path'] ?? '') ?>" target="_blank">
                                        <i class="fa-solid fa-file-download"></i> 
                                        <?= htmlspecialchars($file['file_name'] ?? 'Archivo') ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-files">No hay archivos.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                <!-- ─── BLOQUE DE CONTROL DE TRABAJO (solo staff) ─────────────── -->
                <div class="info-ticket" id="work-session-box">
                    <div class="headerInfo">
                        <i class="fa-solid fa-clock"></i>
                        <p>TIEMPO DE TRABAJO</p>
                    </div>

                    <div class="mainInfo">
                        <p>Ronda</p>
                        <p><strong><?= (int)$ticket['ronda'] ?></strong></p>
                    </div>

                    <div class="mainInfo">
                        <p>Tiempo esta ronda</p>
                        <p id="work-total-ronda">Cargando...</p>
                    </div>

                    <div class="mainInfo">
                        <p>Tiempo total global</p>
                        <p id="work-total-global">Cargando...</p>
                    </div>

                    <!-- Cronómetro activo -->
                    <div class="mainInfo" id="work-chrono-row" style="display:none;">
                        <p>Tiempo sesión</p>
                        <p><strong id="work-chrono">00:00:00</strong></p>
                    </div>

                    <!-- Mensajes de estado -->
                    <div id="work-status-msg" style="padding: 8px 16px; font-size: 13px; color: #666;"></div>

                    <!-- Botones de acción -->
                    <div class="footerInfoTicket" id="work-buttons" style="flex-direction: column; gap: 8px;">
                        <!-- Se renderizan dinámicamente desde JS -->
                    </div>
                </div>

                <!-- Historial de sesiones -->
                <div class="info-ticket" id="work-history-box">
                    <div class="headerInfo">
                        <i class="fa-solid fa-list-check"></i>
                        <p>HISTORIAL DE TRABAJO</p>
                    </div>
                    <div id="work-history-content" style="padding: 8px 16px; font-size: 13px;">
                        Cargando...
                    </div>
                </div>
                <?php endif; ?>
                <!-- ─────────────────────────────────────────────────────────── -->

            </div>
        </div>
    </div>
</div>

<script>
    const TICKET_ID = <?= (int)$ticket['id'] ?>;
    const USER_ID   = <?= (int)$_SESSION['user_id'] ?>;
    const IS_CLOSED = <?= $ticket['status'] === 'closed' ? 'true' : 'false' ?>;
</script>
<script src="/js/ticket.js"></script>