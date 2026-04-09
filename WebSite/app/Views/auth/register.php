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
            <h1>Registro</h1>
            <p>Crea tu cuenta en DonDigital</p>
        </div>

        <form action="/auth/store" method="POST">
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
                    <div class="form-group">
                        <label>Empresa (Opcional)</label>
                        <input type="text" name="company" placeholder="Empresa (Opcional)">
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
                        <label>Nº NIF/CIF/NIE</label>
                        <input type="text" name="nif" required placeholder="DNI / NIF">
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
                        <label>Ciudad</label>
                        <input type="text" name="city" required placeholder="Ciudad">
                    </div>
                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" name="state" required placeholder="Provincia">
                    </div>
                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" name="postcode" required placeholder="C.P.">
                    </div>
                    <div class="form-group">
                        <label>País</label>
                        <select name="country" class="custom-select">
                            <option value="ES">España</option>
                            <option value="PT">Portugal</option>
                            <option value="FR">Francia</option>
                        </select>
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
                        <li id="req-upper" class="invalid"><i class="fas fa-circle"></i> Una mayúscula</li>
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

    <?php if(isset($_SESSION['flash'])): ?>
        <div id="flash-data" 
             data-type="<?= $_SESSION['flash']['type'] ?>" 
             data-msg="<?= $_SESSION['flash']['msg'] ?>" 
             style="display:none;">
        </div>
    <?php unset($_SESSION['flash']); endif; ?>

    <script src="/js/auth.js"></script>
</body>
</html>