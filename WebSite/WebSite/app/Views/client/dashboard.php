<div class="main-wrapper">
    <main class="content">

        <section class="info-box">
            <div class="box tickets">
                <p id="tickets-number"><?= $stats['tickets_count'] ?></p>
                <p>Tickets de Soporte</p>
            </div>
        </section>

        <section class="info">
            <div class="contentInfoBox">
                <div class="headerInfo">
                    <div class="headerInfo-left">
                        <i class="fa-solid fa-ticket"></i>
                        <p>TICKETS DE SOPORTE - RECIENTES</p>
                    </div>
                    <button><a href="/support/option_tickets">+ ABRIR TICKET</a></button>
                </div>
                <div class="bodyInfo">
                    <?php if (!empty($tickets_lista)): ?>
                        <?php foreach ($tickets_lista as $t): ?>
                            <a href="/support/ticket?ticketId=<?= $t['id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="infoTicket">
                                    <div class="upperInfoTicket">
                                        <p>#<?= $t['id'] ?></p>
                                        <p>-</p>
                                        <p><?= htmlspecialchars($t['asunto']) ?></p>
                                        <p class="statusTicket status-<?= $t['status'] ?>">
                                            <?php
                                                echo ($t['status'] == 'open') ? 'Abierto' :
                                                    (($t['status'] == 'answered') ? 'Respondido' :
                                                    (($t['status'] == 'customer-reply') ? 'Resp. Cliente' : 'Cerrado'));
                                            ?>
                                        </p>
                                    </div>
                                    <div class="lastUpdateTicket">
                                        <p>Última Actualización:</p>
                                        <p><?= date('d/m/Y H:i', strtotime($t['fecha'])) ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="padding: 15px; text-align: center; color: #999;">No hay tickets recientes.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="contentInfoBox">
                <div class="headerInfo">
                    <div class="headerInfo-left">
                        <i class="fa-solid fa-newspaper"></i>
                        <p>NOTICIAS RECIENTES</p>
                    </div>
                    <button><i class="fa-solid fa-arrow-right"></i> VER TODO</button>
                </div>
                <div class="bodyInfo"></div>
            </div>
        </section>

        <section class="more-info">
            <div class="box-userInfo boxInfo">
                <div class="headerInfo">
                    <i class="fa-solid fa-user"></i>
                    <p>TU INFORMACIÓN</p>
                </div>
                <div class="bodyInfo">
                    <p><strong>Empresa:</strong> <?= htmlspecialchars($usuario['empresa'] ?? 'No definida') ?></p>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></p>
                    <p><strong>NIF/CIF:</strong> <?= htmlspecialchars($usuario['nif'] ?? 'No definido') ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono'] ?? 'No definido') ?></p>
                    <p><strong>Idioma:</strong> <?= htmlspecialchars($usuario['idioma']) ?></p>
                </div>
                <div class="footerInfo" id="updateAccBtn">
                    <button onclick="window.location.href='/client/accdetails'">
                        <i class="fa-solid fa-pencil"></i> Actualizar
                    </button>
                </div>
            </div>

            <div class="direcAcces boxInfo">
                <div class="headerInfo">
                    <i class="fa-solid fa-bookmark"></i>
                    <p>ACCESOS DIRECTOS</p>
                </div>
                <a href="/support/option_tickets"><i class="fa-solid fa-ticket"></i> Abrir Ticket</a>
                <a href="/support/tickets"><i class="fa-solid fa-list"></i> Mis Tickets</a>
                <a href="/client/accdetails"><i class="fa-solid fa-user"></i> Mi Cuenta</a>
                <a href="/auth/logout"><i class="fa-solid fa-arrow-left"></i> Salir</a>
            </div>
        </section>

    </main>
</div>