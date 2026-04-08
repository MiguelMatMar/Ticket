<section class="left-bar">
    <div class="desplegarSideBar">
        <i class="fa-solid fa-angle-left"></i>
    </div>
     
    <nav class="side-menu">

        <?php if ($_SESSION['user_role'] === 'cliente'): ?>
        <!-- ── MENÚ CLIENTE ── -->

        <div class="menu-item">
            <a href="/client/index"><i class="fa-solid fa-house"></i> Área Clientes</a>
        </div>

        <div class="menu-item has-sub">
            <div class="parent-box">
                <a href="#"><i class="fa-solid fa-headset"></i> Soporte</a>
                <div class="icons-container">
                    <i class="angle-down fa-solid fa-angle-down"></i>
                    <i class="angle-up fa-solid fa-angle-up nonvisible"></i>
                </div>
            </div>
            <div class="sub-menu nonvisible">
                <a href="/support/tickets">Mis Tickets</a>
                <a href="/support/option_tickets">Abrir Ticket</a>
                <a href="/support/faq">FAQ</a>
            </div>
        </div>

        <?php elseif ($_SESSION['user_role'] === 'soporte'): ?>
        <!-- ── MENÚ SOPORTE ── -->

        <div class="menu-item">
            <a href="/client/index"><i class="fa-solid fa-house"></i> Inicio</a>
        </div>

        <div class="menu-item has-sub">
            <div class="parent-box">
                <a href="#"><i class="fa-solid fa-headset"></i> Soporte</a>
                <div class="icons-container">
                    <i class="angle-down fa-solid fa-angle-down"></i>
                    <i class="angle-up fa-solid fa-angle-up nonvisible"></i>
                </div>
            </div>
            <div class="sub-menu nonvisible">
                <a href="/support/tickets">Todos los Tickets</a>
                <a href="/support/option_tickets">Abrir Ticket</a>
                <a href="/support/faq">FAQ</a>
            </div>
        </div>

        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
        <!-- ── MENÚ ADMIN ── -->

        <div class="menu-item">
            <a href="/client/index"><i class="fa-solid fa-house"></i> Inicio</a>
        </div>

        <div class="menu-item has-sub">
            <div class="parent-box">
                <a href="#"><i class="fa-solid fa-headset"></i> Soporte</a>
                <div class="icons-container">
                    <i class="angle-down fa-solid fa-angle-down"></i>
                    <i class="angle-up fa-solid fa-angle-up nonvisible"></i>
                </div>
            </div>
            <div class="sub-menu nonvisible">
                <a href="/support/tickets">Todos los Tickets</a>
                <a href="/support/option_tickets">Abrir Ticket</a>
                <a href="/support/faq">FAQ</a>
            </div>
        </div>

        <div class="menu-item has-sub">
            <div class="parent-box">
                <a href="#"><i class="fa-solid fa-shield-halved"></i> Administración</a>
                <div class="icons-container">
                    <i class="angle-down fa-solid fa-angle-down"></i>
                    <i class="angle-up fa-solid fa-angle-up nonvisible"></i>
                </div>
            </div>
            <div class="sub-menu nonvisible">
                <a href="/support/users">Gestión de Usuarios</a>
            </div>
        </div>

        <?php endif; ?>

    </nav>
</section>