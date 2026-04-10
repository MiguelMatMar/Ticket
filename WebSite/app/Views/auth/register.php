<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - DonDigital</title>
    <link rel="stylesheet" href="/css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/2c7dfaf499.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="registration-container">
        <div class="panel-header">
            <h1 id="register-title">Registro</h1>
            <p>Crea tu cuenta en DonDigital</p>
        </div>

        <!-- Toggle persona / empresa -->
        <div class="type-toggle-wrapper">
            <div class="type-toggle">
                <a href="/auth/register?type=persona" id="btn-persona">
                    <i class="fa-solid fa-user"></i> Persona Física
                </a>
                <a href="/auth/register?type=empresa" id="btn-empresa">
                    <i class="fa-solid fa-building"></i> Empresa
                </a>
            </div>
        </div>

        <form action="/auth/store" method="POST">
            <!-- Campo oculto para enviar el tipo al backend -->
            <input type="hidden" name="type" id="input-type" value="">

            <div class="form-cols-wrapper">
                <div class="form-col">
                    <h3 class="section-title">Detalles del Cliente</h3>
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="name" required placeholder="Tu nombre">
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="surnames" required placeholder="Tus Apellidos">
                    </div>

                    <!-- Solo visible si type=empresa — auth.js lo gestiona -->
                    <div class="form-group" id="group-company" style="display:none;">
                        <label>Empresa</label>
                        <input type="text" name="company" placeholder="Nombre de la empresa">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label>Número de Teléfono</label>
                        <input type="tel" name="telephoneNumber" required placeholder="Número de Teléfono">
                    </div>
                    <div class="form-group">
                        <!-- Etiqueta y placeholder cambian según tipo — auth.js lo gestiona -->
                        <label id="label-nif">DNI</label>
                        <input type="text" name="nif" id="input-nif" required placeholder="DNI">
                    </div>
                </div>

                <div class="form-col">
                    <h3 class="section-title">Dirección de Facturación</h3>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="address1" required placeholder="Calle, número...">
                    </div>
                    <div class="form-group">
                        <label>Dirección 2</label>
                        <input type="text" name="address2" placeholder="Piso, puerta, bloque...">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <select id="country-code-select" class="custom-select" required>
                            <option value="">Seleccione País</option>
                            <option value="es" data-name="España">🇪🇸 España</option>
                            <option value="" disabled>──────────────</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Código Postal</label>
                        <div class="postal-input-wrapper">
                            <input type="text" name="postalCode" id="postal-input" required
                                   placeholder="Selecciona país primero"
                                   maxlength="10"
                                   autocomplete="postal-code"
                                   disabled>
                            <span class="postal-spinner" id="postal-spinner">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </span>
                            <span class="postal-ok" id="postal-ok">
                                <i class="fa-solid fa-circle-check"></i>
                            </span>
                            <span class="postal-error" id="postal-error">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </span>
                        </div>
                        <p class="postal-hint" id="postal-hint">Primero selecciona el país.</p>
                    </div>
                    <!-- País como texto autocompletado (se envía al backend) -->
                    <input type="hidden" name="country" id="country-input">
                    <div class="form-group">
                        <label>Provincia / Estado</label>
                        <input type="text" name="state" id="state-input" required
                               readonly placeholder="Se autocompleta" class="autofilled">
                    </div>
                    <div class="form-group">
                        <label>Ciudad</label>
                        <input type="text" name="city" id="city-input" required
                               readonly placeholder="Se autocompleta" class="autofilled">
                    </div>
                </div>
            </div>

            <!-- Sección de Contacto -->
            <div class="contact-section">
                <h3 class="section-title">Información de Contacto</h3>
                <div class="form-cols-wrapper">
                    <div class="form-col">
                        <div class="form-group">
                            <label>Teléfono Móvil</label>
                            <input type="tel" name="mobilePhone" required placeholder="Teléfono móvil">
                        </div>
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <div class="input-with-check">
                                <input type="tel" name="whatsapp" id="whatsapp-input" required placeholder="Número de WhatsApp">
                                <label class="same-as-mobile">
                                    <input type="checkbox" id="whatsapp-same"> Igual al móvil
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>Email de Contacto</label>
                            <input type="email" name="contactEmail" required placeholder="contacto@email.com">
                        </div>
                        <div class="form-group">
                            <p class="contact-note">
                                <i class="fa-solid fa-circle-info"></i>
                                El email principal (arriba) se usa para acceder a tu cuenta. El email de contacto es el que usaremos para comunicarnos contigo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="security-section">
                <h3 class="section-title">Seguridad de la Cuenta</h3>
                <div class="form-group password-container">
                    <label>Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="********">
                        <i class="fa-solid fa-eye" id="togglePassword"></i>
                    </div>

                    <div class="password-strength-wrapper">
                        <div class="strength-bar"><div id="strength-meter"></div></div>
                        <p id="strength-text">Seguridad: <span>Muy débil</span></p>
                    </div>

                    <ul class="password-requirements">
                        <li id="req-length" class="invalid"><i class="fas fa-circle"></i> Mínimo 8 caracteres</li>
                        <li id="req-upper"  class="invalid"><i class="fas fa-circle"></i> Una mayúscula</li>
                        <li id="req-number" class="invalid"><i class="fas fa-circle"></i> Un número</li>
                        <li id="req-special" class="invalid"><i class="fas fa-circle"></i> Un carácter especial (@$!%*?&)</li>
                    </ul>

                    <label style="margin-top:15px; display:block;">Confirmar Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="********">
                        <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit">Registrarse</button>
                <div class="footer-link">
                    ¿Ya tienes cuenta? <a href="/auth/index">Inicia sesión</a>
                </div>
            </div>
        </form>
    </div>
    <script src="/js/auth.js"></script>
</body>
</html>