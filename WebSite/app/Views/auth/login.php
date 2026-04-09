<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Clientes - Don Digital</title>
    <link rel="stylesheet" href="/css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/2c7dfaf499.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="login-container">
        <h1>DonDigital</h1>
        <p>Área de Clientes</p>

        <form action="/auth/login" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="tu@email.com">
            </div>
            
            <div class="form-group password-container">
                <label>Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="********">
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </div>
            </div>

            <div class="remember-container">
                <input type="checkbox" name="remember_me" id="remember_me" value="1">
                <label for="remember_me">Recordar mi sesión</label>
            </div>
            
            <button type="submit">Iniciar Sesión</button>
        </form>

        <div class="footer-link">
            <p>¿No tienes cuenta? <a href="#" id="openRegisterModal">Regístrate aquí</a></p>
            <br>
            <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
    </div>

    <div id="typeModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <h2>Tipo de Registro</h2>
            <p>Seleccione cómo desea registrarse en DonDigital</p>
            <div class="modal-options">
                <div class="modal-card" onclick="goToRegister('persona')">
                    <i class="fas fa-user"></i>
                    <h3>Persona Física</h3>
                </div>
                <div class="modal-card" onclick="goToRegister('empresa')">
                    <i class="fas fa-building"></i>
                    <h3>Empresa</h3>
                </div>
            </div>
            <button class="btn-close-modal" id="closeModal">Cancelar</button>
        </div>
    </div>

    <?php if(isset($_SESSION['flash'])): ?>
        <div id="flash-data" 
            data-type="<?= $_SESSION['flash']['type'] ?>" 
            data-msg="<?= htmlspecialchars($_SESSION['flash']['msg']) ?>" 
            style="display:none;">
        </div>
    <?php unset($_SESSION['flash']); endif; ?>

    <script src="/js/auth.js"></script>
</body>
</html>