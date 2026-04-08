document.addEventListener('DOMContentLoaded', () => {
    drop_down();
    initHamburger();
    loadNotifications();
    setInterval(loadNotifications, 20000);
});

function drop_down() {
    // 1. Apartado del perfil
    let drop = document.getElementById('drop-down');
    let iconDown = document.getElementById('angle-down');
    let iconUp = document.getElementById('angle-up');
    let dropbox = document.getElementById('drop-down-box');

    // 2. Apartado de notificaciones
    let notificationIcon = document.getElementById('notification-icon');
    let notificationBox = document.getElementById('notification-box');

    function closeAll() {
        [dropbox, notificationBox].forEach(box => box.classList.add('nonvisible'));

        if (iconDown) iconDown.classList.remove('nonvisible');
        if (iconUp) iconUp.classList.add('nonvisible');

        notificationIcon.classList.remove('dropped');
    }

    // EVENTO PERFIL
    if (drop) {
        drop.addEventListener('click', (e) => {
            e.stopPropagation();
            let isOpen = !dropbox.classList.contains('nonvisible');
            closeAll();
            if (!isOpen) {
                dropbox.classList.remove('nonvisible');
                if (iconDown) iconDown.classList.add('nonvisible');
                if (iconUp) iconUp.classList.remove('nonvisible');
            }
        });
    }

    // EVENTO NOTIFICACIONES
    notificationIcon.addEventListener('click', (e) => {
        e.stopPropagation();
        let isOpen = !notificationBox.classList.contains('nonvisible');
        closeAll();
        if (!isOpen) {
            notificationBox.classList.remove('nonvisible');
            notificationIcon.classList.add('dropped');
        }
    });

    document.addEventListener('click', () => {
        closeAll();
    });
}

/**
 * Inicializa el menú hamburguesa para móvil.
 * Abre y cierra el sidebar con overlay.
 */
function initHamburger() {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar      = document.querySelector('.left-bar');
    const overlay      = document.getElementById('sidebar-overlay');

    if (!hamburgerBtn || !sidebar || !overlay) return;

    hamburgerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // Cerrar sidebar al hacer click en un enlace del menú (móvil)
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });
    });
}

/**
 * Consulta al servidor las notificaciones no leídas del usuario
 * y actualiza el badge y la lista del navbar.
 */
function loadNotifications() {
    fetch('/notification/getNotifications')
        .then(res => res.json())
        .then(data => {
            updateBadge(data.total);
            renderNotifications(data.notifications);
        })
        .catch(err => console.error('Error cargando notificaciones:', err));
}

/**
 * Actualiza el número del badge de notificaciones.
 */
function updateBadge(total) {
    const badge = document.getElementById('notif-count');
    if (total > 0) {
        badge.textContent = total;
        badge.classList.remove('nonvisible');
    } else {
        badge.classList.add('nonvisible');
    }
}

/**
 * Renderiza la lista de notificaciones dentro del panel del navbar.
 */
function renderNotifications(notifications) {
    const body = document.querySelector('#notification-box .body-notification');

    if (notifications.length === 0) {
        body.innerHTML = '<p>No tienes notificaciones en este momento</p>';
        return;
    }

    body.innerHTML = '';
    notifications.forEach(n => {
        const item = document.createElement('div');
        item.classList.add('notification-item');
        item.innerHTML = `
            <p class="notif-mensaje">${n.mensaje}</p>
            <p class="notif-fecha">${formatDate(n.created_at)}</p>
        `;
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            markAsRead(n.id, n.ticket_id);
        });
        body.appendChild(item);
    });
}

/**
 * Marca una notificación como leída y redirige al ticket.
 */
function markAsRead(notificationId, ticketId) {
    fetch('/notification/markAsRead', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${notificationId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/support/ticket?ticketId=${ticketId}`;
        }
    })
    .catch(err => console.error('Error marcando notificación:', err));
}

/**
 * Formatea una fecha en formato legible en español.
 */
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}