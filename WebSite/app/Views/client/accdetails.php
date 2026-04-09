<div class="main-content-wrapper">
    <div id="main-content">
        <h1 class="page-title">Detalles de la cuenta</h1>

        <div class="layout-container">
            <div class="content-left">
                <div class="custom-panel">
                    <div class="custom-panel-body">
                        <form action="/client/updateProfile" method="POST">
                            
                            <div class="section-header">
                                <h3>Información Personal</h3>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group-custom">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                </div>
                                <div class="form-group-custom">
                                    <label>Apellido</label>
                                    <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
                                </div>
                                <?php if($usuario['empresa'] != ''): ?> 
                                    <div class="form-group-custom">
                                        <label>Empresa</label>
                                        <input type="text" name="empresa" class="form-control" placeholder="Nombre de la empresa" value="<?= htmlspecialchars($usuario['empresa']) ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="form-group-custom">
                                    <label>Dirección de E-Mail</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required readonly>
                                </div>
                                <div class="form-group-custom">
                                    <label>Número de Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>NIF / CIF / NIE</label>
                                    <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($usuario['nif'] ?? '') ?>" placeholder="Ej: 12345678Z">
                                </div>
                            </div>

                            <div class="section-header">
                                <h3>Dirección de Facturación</h3>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-custom span-2">
                                    <label>Dirección 1</label>
                                    <input type="text" name="direccion1" class="form-control" value="<?= htmlspecialchars($usuario['direccion1'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Dirección 2</label>
                                    <input type="text" name="direccion2" class="form-control" value="<?= htmlspecialchars($usuario['direccion2'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Ciudad</label>
                                    <select name="ciudad" id="ciudad" class="form-control" data-value="<?= htmlspecialchars($usuario['ciudad'] ?? '') ?>">
                                        <option value="">—</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Provincia/Región</label>
                                    <select name="provincia" id="provincia" class="form-control" data-value="<?= htmlspecialchars($usuario['provincia'] ?? '') ?>">
                                        <option value="">—</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Código Postal</label>
                                    <input type="text" name="codigo_postal" class="form-control" value="<?= htmlspecialchars($usuario['codigo_postal'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>País</label>
                                    <select name="pais" id="pais" class="form-control" data-value="<?= htmlspecialchars($usuario['pais'] ?? 'España') ?>">
                                        <option value="">—</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Contacto de Facturación Predeterminado</label>
                                    <select name="contacto_facturacion" class="form-control">
                                        <option value="default">Contacto por defecto</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Idioma</label>
                                    <select name="idioma" class="form-control">
                                        <option value="es" <?= (isset($usuario['idioma']) && $usuario['idioma'] == 'es') ? 'selected' : '' ?>>Español</option>
                                        <option value="en" <?= (isset($usuario['idioma']) && $usuario['idioma'] == 'en') ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save">Guardar cambios</button>
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
                        <a href="/client/accdetails" class="sidebar-item active">Detalles de la cuenta</a>
                        <a href="/client/changepassword" class="sidebar-item">Cambiar Contraseña</a>
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