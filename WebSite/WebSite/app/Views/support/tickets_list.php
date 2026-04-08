<div class="content-tickets">
    <div class="header-main">
        <h1><?= (isset($userRole) && in_array($userRole, ['soporte', 'admin'])) ? 'Todos los Tickets' : 'Mis Tickets de Soporte' ?></h1>
    </div>
    <div class="breadcrumb">
        <p>Administración</p>
        <p>></p>
        <p><?= (isset($userRole) && in_array($userRole, ['soporte', 'admin'])) ? 'Panel de Soporte' : 'Área de Clientes' ?></p>
        <p>></p>
        <p>Tickets de Soporte</p>
    </div>

    <div class="body-main">
        <div class="body-tickets">
            <h1><?= (isset($userRole) && in_array($userRole, ['soporte', 'admin'])) ? 'TODOS LOS TICKETS' : 'MIS TICKETS DE SOPORTE' ?></h1>
            
            <div class="body-tickets-nav">
                <p id="ticket-table-info">Cargando entradas...</p>
                <div class="navBar">
                    <select id="filter-dept" class="ticket-select-filter">
                        <option value="">Todos los Departamentos</option>
                        <option value="tecnico">Soporte Técnico</option>
                        <option value="dominios">Dominios</option>
                        <option value="gestion">Gestión / Facturación</option>
                        <option value="contacto">Contacto</option>
                        <option value="sugerencias">Sugerencias</option>
                        <option value="afiliados">Afiliados</option>
                    </select>
                    <input type="text" id="ticket-search" placeholder="Buscar ticket...">
                </div>
            </div>
            
            <div class="tickets-table">
                <table id="tableTicketsList" class="display">
                    <thead>
                        <tr>
                            <th>Asunto</th>
                            <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                                <th>Cliente</th>
                            <?php endif; ?>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th>Última Actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($tickets_lista)): ?>
                            <?php foreach ($tickets_lista as $ticket): ?>
                            <tr>
                                <td class="ticket-subject-cell">
                                    <a href="/support/ticket?ticketId=<?= $ticket['id'] ?>">
                                        #<?= $ticket['id'] ?> - <?= htmlspecialchars($ticket['asunto']) ?>
                                    </a>
                                </td>
                                <?php if (isset($userRole) && in_array($userRole, ['soporte', 'admin'])): ?>
                                    <td><?= htmlspecialchars($ticket['user_nombre']) ?></td>
                                <?php endif; ?>
                                <td data-filter="<?= $ticket['departamento'] ?>">
                                    <?= ucfirst($ticket['departamento']) ?>
                                </td>
                                <td data-filter="<?= $ticket['status'] ?>">
                                    <span class="status-badge status-<?= $ticket['status'] ?>">
                                        <?php 
                                            echo ($ticket['status'] == 'open') ? 'Abierto' : 
                                                (($ticket['status'] == 'answered') ? 'Respondido' : 
                                                (($ticket['status'] == 'customer-reply') ? 'Respuesta Cliente' : 'Cerrado')); 
                                        ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($ticket['fecha'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="ticket-footer">
                <div class="entries-selector">
                    <p>Ver 
                        <select id="ticket-length">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="-1">Todo</option>
                        </select> 
                    Entradas</p>
                </div>
                <div class="pagination-controls">
                    <button id="prevTicket" class="btn-nav">Anterior</button>
                    <button id="nextTicket" class="btn-nav">Siguiente</button>
                </div>
            </div>
        </div>
        
        <div class="body-box extraBox">
            <div class="contentInfoBox lookFilter">
                <div class="headerInfo">
                    <i class="fas fa-filter"></i>
                    <p>VER</p>
                </div>
                <div class="lookInfo" onclick="filterByStatus('open')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Abierto</a></div>
                    <p><?= $stats['abiertos'] ?? 0 ?></p>
                </div>
                <div class="lookInfo" onclick="filterByStatus('answered')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Respondido</a></div>
                    <p><?= $stats['contestados'] ?? 0 ?></p>
                </div>
                <div class="lookInfo" onclick="filterByStatus('customer-reply')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Resp. Cliente</a></div>
                    <p><?= $stats['respuesta_cliente'] ?? 0 ?></p>
                </div>
                <div class="lookInfo" onclick="filterByStatus('closed')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Cerrado</a></div>
                    <p><?= $stats['cerrados'] ?? 0 ?></p>
                </div>
                <div class="lookInfo" onclick="filterByStatus('')">
                    <div><i class="fas fa-list"></i><a href="javascript:void(0)">Ver Todos</a></div>
                </div>
            </div>
            
            <div class="contentInfoBox lookFilter">
                <div class="headerInfo">
                    <i class="fa-solid fa-globe"></i>
                    <p>SOPORTE</p>
                </div>
                <div class="bodyInfo">
                    <a href="/support/tickets" class="support-link active">
                        <i class="fa-solid fa-ticket"></i>
                        <?= (isset($userRole) && in_array($userRole, ['soporte', 'admin'])) ? 'Todos los Tickets' : 'Mis Tickets' ?>
                    </a>
                    <a href="/support/option_tickets" class="support-link">
                        <i class="fa-solid fa-comments"></i> Abrir Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>