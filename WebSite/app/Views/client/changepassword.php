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

                                <!-- Contraseña Actual -->
                                <div class="form-group-custom">
                                    <label>Contraseña Actual</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" name="current_password"
                                               class="form-control" required id="current_password">
                                        <i class="fa-solid fa-eye cp-toggle" id="toggleCurrent"></i>
                                    </div>
                                </div>

                                <!-- Nueva Contraseña -->
                                <div class="form-group-custom">
                                    <label>Nueva Contraseña</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" name="new_password"
                                               class="form-control" required id="new_password">
                                        <i class="fa-solid fa-eye cp-toggle" id="toggleNew"></i>
                                    </div>

                                    <!-- Medidor de fortaleza -->
                                    <div class="cp-strength-wrapper">
                                        <div class="cp-strength-bar">
                                            <div id="cp-strength-meter"></div>
                                        </div>
                                        <p id="cp-strength-text">Seguridad: <span>Muy débil</span></p>
                                    </div>

                                    <!-- Requisitos -->
                                    <ul class="cp-requirements">
                                        <li id="cp-req-length"  class="invalid"><i class="fas fa-circle"></i> Mínimo 8 caracteres</li>
                                        <li id="cp-req-upper"   class="invalid"><i class="fas fa-circle"></i> Una mayúscula</li>
                                        <li id="cp-req-number"  class="invalid"><i class="fas fa-circle"></i> Un número</li>
                                        <li id="cp-req-special" class="invalid"><i class="fas fa-circle"></i> Un carácter especial (!@#$%...)</li>
                                    </ul>
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div class="form-group-custom">
                                    <label>Confirmar Contraseña</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" name="confirm_password"
                                               class="form-control" required id="confirm_password">
                                        <i class="fa-solid fa-eye cp-toggle" id="toggleConfirm"></i>
                                    </div>
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
                        <?php if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'soporte'): ?>
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