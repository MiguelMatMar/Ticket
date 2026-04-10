document.addEventListener('DOMContentLoaded', function () {
    handleServerResponse();
    initPasswordToggles();
    initStrengthMeter();
    initFormValidation();
});


// =============================================================================
// 1. ALERTAS — parámetros de URL devueltos por el servidor
// =============================================================================
function handleServerResponse() {
    const params     = new URLSearchParams(window.location.search);
    const successKey = params.get('success');

    if (successKey) {
        const messages = {
            '1':    'La contraseña se ha cambiado correctamente.',
            'role': 'El rol del usuario ha sido actualizado con éxito.'
        };
        const message = messages[successKey];
        if (message) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: message,
                confirmButtonColor: '#18507F',
                timer: 3000,
                timerProgressBar: true
            });
            window.history.replaceState({}, document.title, window.location.pathname);
            return;
        }
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


// =============================================================================
// 2. TOGGLES OJO — mostrar / ocultar contraseña
// =============================================================================
function initPasswordToggles() {
    const toggles = [
        { toggleId: 'toggleCurrent', inputId: 'current_password'  },
        { toggleId: 'toggleNew',     inputId: 'new_password'      },
        { toggleId: 'toggleConfirm', inputId: 'confirm_password'  }
    ];

    toggles.forEach(({ toggleId, inputId }) => {
        const toggle = document.getElementById(toggleId);
        const input  = document.getElementById(inputId);
        if (!toggle || !input) return;

        toggle.addEventListener('click', function () {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
}


// =============================================================================
// 3. MEDIDOR DE FORTALEZA DE CONTRASEÑA
//    (portado de auth.js — misma lógica, mismos umbrales)
// =============================================================================
function initStrengthMeter() {
    const passwordInput = document.getElementById('new_password');
    const meter         = document.getElementById('cp-strength-meter');
    const strengthText  = document.querySelector('#cp-strength-text span');
    const requirements  = {
        length:  document.getElementById('cp-req-length'),
        upper:   document.getElementById('cp-req-upper'),
        number:  document.getElementById('cp-req-number'),
        special: document.getElementById('cp-req-special')
    };

    if (!passwordInput || !meter) return;

    passwordInput.addEventListener('input', function () {
        const val = this.value;
        let score = 0;

        const checks = {
            length:  val.length >= 8,
            upper:   /[A-Z]/.test(val),
            number:  /[0-9]/.test(val),
            special: /\W/.test(val)
        };

        for (const key in checks) {
            if (requirements[key]) {
                requirements[key].classList.toggle('valid',   checks[key]);
                requirements[key].classList.toggle('invalid', !checks[key]);
                if (checks[key]) score++;
            }
        }

        meter.className = '';

        if (val.length === 0) {
            strengthText.textContent = 'Muy débil';
            meter.style.width = '0';
        } else if (score <= 1) {
            strengthText.textContent = 'Débil';
            meter.classList.add('cp-strength-weak');
        } else if (score === 2) {
            strengthText.textContent = 'Media';
            meter.classList.add('cp-strength-medium');
        } else if (score === 3) {
            strengthText.textContent = 'Buena';
            meter.classList.add('cp-strength-good');
        } else if (score === 4) {
            strengthText.textContent = 'Fuerte';
            meter.classList.add('cp-strength-strong');
        }
    });
}


// =============================================================================
// 4. VALIDACIÓN DEL FORMULARIO antes de enviar
// =============================================================================
function initFormValidation() {
    const form = document.getElementById('formChangePassword');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const currentPassword = document.getElementById('current_password').value.trim();
        const newPassword     = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Contraseña actual obligatoria
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

        // Las contraseñas nuevas deben coincidir
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden. Por favor, inténtalo de nuevo.',
                confirmButtonColor: '#18507F'
            });
            return;
        }

        // Debe cumplir los requisitos de seguridad
        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[A-Za-z\d\W]{8,}$/;
        if (!passRegex.test(newPassword)) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Contraseña débil',
                text: 'La contraseña no cumple con todos los requisitos de seguridad.',
                confirmButtonColor: '#18507F'
            });
        }
    });
}