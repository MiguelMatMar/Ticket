<div class="main-content-wrapper">
    <div id="main-content">
        <h1 class="page-title">Detalles de la cuenta</h1>

        <div class="layout-container">
            <div class="content-left">
                <div class="custom-panel">
                    <div class="custom-panel-body">
                        <form action="/client/updateProfile" method="POST">

                            <!-- ===================== INFORMACIÓN PERSONAL ===================== -->
                            <div class="section-header">
                                <h3>Información Personal</h3>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-custom">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control"
                                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                </div>
                                <div class="form-group-custom">
                                    <label>Apellidos</label>
                                    <input type="text" name="apellidos" class="form-control"
                                           value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
                                </div>

                                <?php if (!empty($usuario['empresa'])): ?>
                                <div class="form-group-custom">
                                    <label>Empresa</label>
                                    <input type="text" name="empresa" class="form-control"
                                           value="<?= htmlspecialchars($usuario['empresa']) ?>">
                                </div>
                                <?php endif; ?>

                                <div class="form-group-custom">
                                    <label>Dirección de E-Mail</label>
                                    <input type="email" name="email" class="form-control"
                                           value="<?= htmlspecialchars($usuario['email']) ?>" required readonly>
                                </div>
                                <div class="form-group-custom">
                                    <label>Número de Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control"
                                           value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>NIF / CIF / NIE</label>
                                    <input type="text" name="nif" class="form-control"
                                           value="<?= htmlspecialchars($usuario['nif'] ?? '') ?>"
                                           placeholder="Ej: 12345678Z">
                                </div>
                            </div>

                            <!-- ===================== DIRECCIÓN DE FACTURACIÓN ===================== -->
                            <div class="section-header">
                                <h3>Dirección de Facturación</h3>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-custom span-2">
                                    <label>Dirección 1</label>
                                    <input type="text" name="direccion1" class="form-control"
                                           value="<?= htmlspecialchars($usuario['direccion1'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Dirección 2</label>
                                    <input type="text" name="direccion2" class="form-control"
                                           value="<?= htmlspecialchars($usuario['direccion2'] ?? '') ?>">
                                </div>

                                <!-- País -->
                                <div class="form-group-custom">
                                    <label>País</label>
                                    <select id="profile-country-select" name="pais" class="form-control" required>
                                        <option value="">Seleccione País</option>
                                        <option value="es" data-name="España"
                                            <?= ($usuario['pais'] ?? '') === 'España' ? 'selected' : '' ?>>
                                            🇪🇸 España
                                        </option>
                                        <option value="" disabled>──────────────</option>
                                        <!-- El resto de países se pobla desde JS -->
                                    </select>
                                    <!-- Campo hidden que almacena el nombre legible del país -->
                                    <input type="hidden" name="pais_nombre" id="profile-country-name"
                                           value="<?= htmlspecialchars($usuario['pais'] ?? '') ?>">
                                </div>

                                <!-- Código Postal -->
                                <div class="form-group-custom">
                                    <label>Código Postal</label>
                                    <div class="postal-input-wrapper">
                                        <input type="text" name="codigo_postal" id="profile-postal-input"
                                               class="form-control"
                                               value="<?= htmlspecialchars($usuario['codigo_postal'] ?? '') ?>"
                                               placeholder="Selecciona país primero"
                                               maxlength="10"
                                               autocomplete="postal-code"
                                               <?= empty($usuario['pais']) ? 'disabled' : '' ?>>
                                        <span class="postal-spinner" id="profile-postal-spinner">
                                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                                        </span>
                                        <span class="postal-ok" id="profile-postal-ok">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </span>
                                        <span class="postal-error" id="profile-postal-error">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        </span>
                                    </div>
                                    <p class="postal-hint" id="profile-postal-hint">
                                        <?= empty($usuario['pais']) ? 'Primero selecciona el país.' : 'Escribe tu código postal y rellenaremos el resto.' ?>
                                    </p>
                                </div>

                                <!-- Provincia — readonly, se autocompleta -->
                                <div class="form-group-custom">
                                    <label>Provincia / Estado</label>
                                    <input type="text" name="provincia" id="profile-state-input"
                                           class="form-control autofilled"
                                           value="<?= htmlspecialchars($usuario['provincia'] ?? '') ?>"
                                           readonly placeholder="Se autocompleta">
                                </div>

                                <!-- Ciudad — readonly, se autocompleta -->
                                <div class="form-group-custom">
                                    <label>Ciudad</label>
                                    <input type="text" name="ciudad" id="profile-city-input"
                                           class="form-control autofilled"
                                           value="<?= htmlspecialchars($usuario['ciudad'] ?? '') ?>"
                                           readonly placeholder="Se autocompleta">
                                </div>

                                <div class="form-group-custom">
                                    <label>Idioma</label>
                                    <select name="idioma" class="form-control">
                                        <option value="es" <?= (($usuario['idioma'] ?? '') === 'es') ? 'selected' : '' ?>>Español</option>
                                        <option value="en" <?= (($usuario['idioma'] ?? '') === 'en') ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ===================== INFORMACIÓN DE CONTACTO ===================== -->
                            <div class="section-header">
                                <h3>Información de Contacto</h3>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-custom">
                                    <label>Teléfono Móvil</label>
                                    <input type="tel" name="telefono_movil" id="profile-mobile-input"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['telefono_movil'] ?? '') ?>"
                                           placeholder="Teléfono móvil">
                                </div>

                                <div class="form-group-custom">
                                    <label>WhatsApp</label>
                                    <div class="input-with-check">
                                        <input type="tel" name="whatsapp" id="profile-whatsapp-input"
                                               class="form-control"
                                               value="<?= htmlspecialchars($usuario['whatsapp'] ?? '') ?>"
                                               placeholder="Número de WhatsApp">
                                        <label class="same-as-mobile" style="margin-top: 6px; display:flex; align-items:center; gap:6px; font-size:.85rem; color:#666;">
                                            <input type="checkbox" id="profile-whatsapp-same"> Igual al móvil
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label>Email de Contacto</label>
                                    <input type="email" name="email_contacto" class="form-control"
                                           value="<?= htmlspecialchars($usuario['email_contacto'] ?? '') ?>"
                                           placeholder="contacto@email.com">
                                </div>

                                <div class="form-group-custom">
                                    <p class="contact-note" style="font-size:.85rem; color:#666; margin-top:8px;">
                                        <i class="fa-solid fa-circle-info"></i>
                                        El email principal se usa para acceder a tu cuenta. El email de contacto es el que usaremos para comunicarnos contigo.
                                    </p>
                                </div>
                            </div>

                            <!-- ===================== ACCIONES ===================== -->
                            <div class="form-actions">
                                <button type="submit" class="btn-save">Guardar cambios</button>
                                <button type="reset" class="btn-cancel">Cancelar</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- ===================== SIDEBAR ===================== -->
            <div class="sidebar-right">
                <div class="sidebar-panel">
                    <div class="sidebar-header">
                        <i class="fas fa-address-card"></i> Cuenta
                    </div>
                    <div class="sidebar-list">
                        <a href="/client/accdetails" class="sidebar-item active">Detalles de la cuenta</a>
                        <a href="/client/changepassword" class="sidebar-item">Cambiar Contraseña</a>
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