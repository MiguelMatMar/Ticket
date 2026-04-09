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
                    <div class="form-group" id="group-company">
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
                        <label>País</label>
                        <select name="country" id="country-select" class="custom-select">
                            <option value="">Seleccione País</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Provincia / Estado</label>
                        <select name="state" id="state-select" class="custom-select" required disabled>
                            <option value="">Seleccione País primero</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ciudad</label>
                        <select name="city" id="city-select" class="custom-select" required disabled>
                            <option value="">Seleccione Provincia primero</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Código Postal</label>
                        <input type="text" name="postalCode" required placeholder="Código Postal">
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
</body>
</html>