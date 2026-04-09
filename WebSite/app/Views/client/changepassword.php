<div class="main-content-wrapper">
    <div id="main-content">
        <h1 class="page-title"><?php echo $title; ?></h1>

        <div class="layout-container">
            <div class="content-left">
                <div class="custom-panel">
                    <div class="custom-panel-body">
                        <form action="/client/updatePassword" method="POST" id="formChangePassword">
                            
                            <div class="section-header">
                                <h3>Cambiar Contraseña</h3>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group-custom">
                                    <label>Contraseña Actual</label>
                                    <input type="password" name="current_password" class="form-control" required id="current_password">
                                </div>
                                <div class="form-group-custom">
                                    <label>Nueva Contraseña</label>
                                    <input type="password" name="new_password" class="form-control" required id="new_password">
                                    <small style="color:#888; margin-top:5px;">Mín. 8 caracteres, mayúscula, minúscula, número y especial (@$!%*?&)</small>
                                </div>
                                <div class="form-group-custom">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" name="confirm_password" class="form-control" required id="confirm_password">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save" id="updatePasswdBtn">Guardar cambios</button>
                                <button type="reset" class="btn-cancel">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="sidebar-right">
                <div class="sidebar-panel">
                    <div class="sidebar-header">
                        <i class="fas fa-address-card"></i> Cuenta
                    </div>
                    <div class="sidebar-list">
                        <a href="/client/accdetails" class="sidebar-item">Detalles de la cuenta</a>
                        <a href="/client/changepassword" class="sidebar-item active">Cambiar Contraseña</a>
                        <?php if($usuario['rol'] === 'admin' || $usuario['rol'] === "soporte"): ?>
                            <a href="#" class="sidebar-item">Gestión de usuarios</a>
                            <a href="#" class="sidebar-item">Contactos</a> 
                        <?php endif; ?>
                        <a href="#" class="sidebar-item">Seguridad de la cuenta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>