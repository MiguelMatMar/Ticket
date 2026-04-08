<div class="content-formTicket">
    <div class="header-main">
        <h1>Abrir Ticket</h1>
    </div>
    <div class="breadcrumb">
        <p>Administración > Area Cliente > Abrir Ticket</p>
    </div>

    <div class="body-main ticketform">
        <div class="contentForm">
            <h2>ABRIR TICKET</h2>
            
            <form action="/support/store_ticket" method="POST" class="ticket-form" enctype="multipart/form-data">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nameUser">Nombre</label>
                        <input type="text" name="nameUser" id="nameUser" value="<?= htmlspecialchars($usuario['nombre']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="emailUser">Dirección de Email</label> 
                        <input type="email" name="emailUser" id="emailUser" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>    
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="affairUser">Asunto</label>
                    <input type="text" name="affairUser" id="affairUser" placeholder="¿En qué podemos ayudarte?" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="departmentUser">Departamento</label>
                        <select name="departmentUser" id="departmentUser">
                            <option value="tecnico" <?php echo ($optionTicket == 'tecnico') ? 'selected' : ''; ?>>Soporte Técnico</option>
                            <option value="dominios" <?php echo ($optionTicket == 'dominios') ? 'selected' : ''; ?>>Dominios</option>
                            <option value="gestion" <?php echo ($optionTicket == 'gestion') ? 'selected' : ''; ?>>Gestión / Facturación</option>
                            <option value="contacto" <?php echo ($optionTicket == 'contacto') ? 'selected' : ''; ?>>Contacto</option>
                            <option value="sugerencias" <?php echo ($optionTicket == 'sugerencias') ? 'selected' : ''; ?>>Sugerencias</option>
                            <option value="afiliados" <?php echo ($optionTicket == 'afiliados') ? 'selected' : ''; ?>>Afiliados</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Prioridad</label>
                        <select name="priority" id="priority">
                            <option value="lowPriority">Baja</option>
                            <option value="mediumPriority" selected>Media</option>
                            <option value="highPriority">Alta</option>
                            <option value="criticalPriority">Crítica</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="messageUser">Mensaje</label>
                    <textarea name="messageUser" id="messageUser"></textarea>
                </div>

                <div class="form-group" id="files">
                    <label for="fileUsers">Adjuntos</label>
                    <input type="file" name="fileUsers[]" class="file-input">
                    <button type="button" class="btn-submit" id="addFile"> + AÑADIR MÁS </button>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Enviar Ticket</button>
                    <button type="reset" class="btn-cancel">Cancelar</button>
                </div>
            </form>
        </div>
        
        <div class="body-box extraBox">
            <div class="contentInfoBox">
                <div class="headerInfo">
                    <i class="fa-solid fa-info"></i>
                    <p>TICKETS</p>
                </div>
                <div class="bodyInfo">
                    <?php if (!empty($tickets_lista)): ?>
                        <?php foreach ($tickets_lista as $ticket): ?>
                            <div class="infoTicket">
                                <div class="upperInfoTicket">
                                    <p>#<?= htmlspecialchars($ticket['id']) ?></p>
                                    <p>-</p>
                                    <p><?= htmlspecialchars($ticket['asunto']) ?></p>
                                    <p class="statusTicket"><?= htmlspecialchars($ticket['status']) ?></p>
                                </div>
                                <div class="lastUpdateTicket">
                                    <p>Ultima Actualizacion: </p>
                                    <p><?= date('d/m/Y (H:i)', strtotime($ticket['fecha'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No tienes tickets recientes.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="contentInfoBox">
                <div class="headerInfo">
                    <i class="fa-solid fa-globe"></i>
                    <p>SOPORTE</p>
                </div>
                <div class="bodyInfo">
                    <a href="#">
                        <i class="fa-solid fa-ticket"></i>
                        Mis Tickets de Soporte
                    </a>
                    <a href="#">
                        <i class="fa-solid fa-circle-info"></i>
                        Preguntas Frecuentes - FAQ
                    </a>
                    <a href="#">
                        <i class="fa-solid fa-comments"></i>
                        Abrir Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>