document.addEventListener('DOMContentLoaded', function() {
    handleServerResponse();
    initFormValidation();
});

/**
 * Lee los parámetros de la URL que devuelve el servidor
 * y muestra el SweetAlert correspondiente.
 */
function handleServerResponse() {
    const params = new URLSearchParams(window.location.search);

    if (params.get('success') === '1') {
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'La contraseña se ha cambiado correctamente.',
            confirmButtonColor: '#18507F',
            timer: 3000,
            timerProgressBar: true
        });

        // Limpiar el parámetro de la URL sin recargar
        window.history.replaceState({}, document.title, window.location.pathname);
        return;
    }

    const errorMessages = {
        'current': 'La contraseña actual no es correcta.',
        'confirm': 'La nueva contraseña y la confirmación no coinciden.',
        'weak':    'La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
        'db':      'Ha ocurrido un error al guardar. Inténtalo de nuevo.'
    };

    const errorKey = params.get('error');
    if (errorKey && errorMessages[errorKey]) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessages[errorKey],
            confirmButtonColor: '#18507F'
        });

        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

/**
 * Valida el formulario en el cliente antes de enviarlo al servidor.
 * Evita envíos innecesarios si las contraseñas no coinciden.
 */
function initFormValidation() {
    const form = document.getElementById('formChangePassword');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value.trim();
        const newPassword     = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!currentPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La contraseña actual es obligatoria.',
                confirmButtonColor: '#18507F'
            });
            return;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden. Por favor, inténtalo de nuevo.',
                confirmButtonColor: '#18507F'
            });
        }
    });
}