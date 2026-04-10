<div class="content-tickets">
    <div class="header-main">
        <h1>Gestión de Usuarios</h1>
    </div>
    <div class="breadcrumb">
        <p>Administración</p>
        <p>></p>
        <p>Panel Admin</p>
        <p>></p>
        <p>Usuarios</p>
    </div>

    <div class="body-main">
        <div class="body-tickets">
            <h1>LISTADO DE USUARIOS</h1>

            <div class="body-tickets-nav">
                <p id="ticket-table-info">Cargando entradas...</p>
                <div class="navBar">
                    <select id="filter-rol" class="ticket-select-filter">
                        <option value="">Todos los Roles</option>
                        <option value="cliente">Cliente</option>
                        <option value="soporte">Soporte</option>
                        <option value="admin">Admin</option>
                    </select>
                    <input type="text" id="ticket-search" placeholder="Buscar usuario...">
                </div>
            </div>

            <div class="tickets-table">
                <table id="tableTicketsList" class="display">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th class="hide-mobile">Empresa</th>
                            <th class="hide-mobile">Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="hide-mobile">Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users_list)): ?>
                            <?php foreach ($users_list as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellidos']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($u['empresa'] ?? '-') ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($u['telefono'] ?? '-') ?></td>
                                <td>
                                    <!-- Span oculto con el rol: el JS lo lee para filtrar -->
                                    <span class="rol-value" style="display:none"><?= htmlspecialchars($u['rol']) ?></span>
                                    <select class="ticket-select-filter update-role-select" data-user-id="<?= $u['id'] ?>">
                                        <option value="cliente" <?= $u['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                        <option value="soporte" <?= $u['rol'] === 'soporte' ? 'selected' : '' ?>>Soporte</option>
                                        <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </td>
                                <td>
                                    <span class="status-badge <?= $u['status'] ? 'status-open' : 'status-closed' ?>">
                                        <?= $u['status'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="hide-mobile"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                <td class="actions-cell">
                                    <?php if ($u['status']): ?>
                                        <a href="/support/toggle_user_status?userId=<?= $u['id'] ?>&status=0"
                                           class="btn-action btn-disable"
                                           onclick="return swConfirm(event, this, '¿Desactivar usuario?', '¿Desactivar a <?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>?', 'warning', 'Sí, desactivar')">
                                            <i class="fa-solid fa-ban"></i> <span class="btn-text">Desactivar</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="/support/toggle_user_status?userId=<?= $u['id'] ?>&status=1"
                                           class="btn-action btn-enable"
                                           onclick="return swConfirm(event, this, '¿Activar usuario?', '¿Activar a <?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>?', 'question', 'Sí, activar')">
                                            <i class="fa-solid fa-check"></i> <span class="btn-text">Activar</span>
                                        </a>
                                    <?php endif; ?>

                                    <a href="/support/delete_user?userId=<?= $u['id'] ?>"
                                       class="btn-action btn-delete"
                                       onclick="return swConfirm(event, this, 'Eliminar usuario', '¿Eliminar definitivamente a <?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>? Su email quedará bloqueado para futuros registros.', 'error', 'Sí, eliminar')">
                                        <i class="fa-solid fa-trash"></i> <span class="btn-text">Eliminar</span>
                                    </a>
                                </td>
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
                    <i class="fas fa-users"></i>
                    <p>FILTRAR</p>
                </div>
                <div class="lookInfo" onclick="filterByRol('cliente')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Clientes</a></div>
                    <p><?= count(array_filter($users_list, fn($u) => $u['rol'] === 'cliente')) ?></p>
                </div>
                <div class="lookInfo" onclick="filterByRol('soporte')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Soporte</a></div>
                    <p><?= count(array_filter($users_list, fn($u) => $u['rol'] === 'soporte')) ?></p>
                </div>
                <div class="lookInfo" onclick="filterByRol('admin')">
                    <div><i class="far fa-circle"></i><a href="javascript:void(0)">Admins</a></div>
                    <p><?= count(array_filter($users_list, fn($u) => $u['rol'] === 'admin')) ?></p>
                </div>
                <div class="lookInfo" onclick="filterByRol('')">
                    <div><i class="fas fa-list"></i><a href="javascript:void(0)">Ver Todos</a></div>
                </div>
            </div>

            <div class="contentInfoBox lookFilter">
                <div class="headerInfo">
                    <i class="fa-solid fa-shield-halved"></i>
                    <p>ADMINISTRACIÓN</p>
                </div>
                <div class="bodyInfo">
                    <a href="/support/tickets" class="support-link">
                        <i class="fa-solid fa-ticket"></i> Todos los Tickets
                    </a>
                    <a href="/support/users" class="support-link active">
                        <i class="fa-solid fa-users"></i> Gestión de Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>