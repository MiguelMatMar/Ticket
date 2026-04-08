<nav>

    <div class="icon-nav">
        <p>DonDigital</p>
    </div>

    <section class="right-nav">
        <!-- Botón hamburguesa solo visible en móvil -->
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Menú">
            <i class="fa-solid fa-bars"></i>
        </button>


        <div class="notification-nav" id="notification-icon">
            <p class="number-notification" id="notif-count">0</p>
            <i class="fa-solid fa-bell"></i>
        </div>

        <div class="perfil-nav">
            <div class="img-perfil">
                <i class="fa-regular fa-circle-user"></i>
            </div>

            <div class="info-perfil">
                <p class="user-name" id="user-name"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></p>
                <p class="user-company" id="user-company"><?php echo htmlspecialchars($usuario['empresa']); ?></p>
            </div>
            <div id="drop-down" class="drop-down-icon">
                <i id="angle-down" class="fa-solid fa-angle-down" style="color: rgb(0, 0, 0);"></i>
                <i id="angle-up" class="fa-solid fa-angle-up nonvisible" style="color: rgb(0, 0, 0);"></i>
            </div>
        </div>
    </section>

    <section class="drop-down-box nonvisible" id="drop-down-box">
        <a href="/client/accdetails">
            <i class="fa-solid fa-user"></i>
            <p>Detalles de la cuenta</p>
        </a>
        <?php if($usuario['rol'] === 'admin' || $usuario['rol'] === "soporte"): ?>
            <a href="#">
                <i class="fa-solid fa-address-book"></i>
                <p>Contactos</p>
            </a>
        <?php endif; ?>
        <a href="#">
            <i class="fa-solid fa-user-shield"></i>
            <p>Seguridad de la cuenta</p>
        </a>
        <a href="/client/changepassword">
            <i class="fa-solid fa-wrench"></i>
            <p>Cambiar Contraseña</p>
        </a>
        <a href="/auth/logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <p>Salir</p>
        </a>
    </section>

    <section class="cart-box nonvisible" id="cart-box">
        <div class="header-notification"><p>Carrito de compras</p></div>
        <div class="body-notification"><p>No tienes productos</p></div>
    </section>

    <section class="notification-box nonvisible" id="notification-box">
        <div class="header-notification"><p>Notificaciones</p></div>
        <div class="body-notification"><p>No tienes notificaciones en este momento</p></div>
    </section>

    <!-- Overlay para cerrar sidebar en móvil -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
</nav>