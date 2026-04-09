document.addEventListener('DOMContentLoaded', function() {
    
    function setupPasswordToggle(toggleId, inputId) {
        let toggle = document.querySelector(toggleId);
        let input = document.querySelector(inputId);

        if (toggle && input) {
            toggle.addEventListener('click', function() {
                let type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    }

    setupPasswordToggle('#togglePassword', '#password');
    setupPasswordToggle('#toggleConfirmPassword', '#confirmPassword');

    // Lógica del PowerMeter y Requisitos
    const passwordInput = document.querySelector('#password');
    const meter = document.querySelector('#strength-meter');
    const strengthText = document.querySelector('#strength-text span');
    const requirements = {
        length: document.querySelector('#req-length'),
        upper: document.querySelector('#req-upper'),
        number: document.querySelector('#req-number'),
        special: document.querySelector('#req-special')
    };

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let score = 0;

            const checks = {
                length: val.length >= 8,
                upper: /[A-Z]/.test(val),
                number: /[0-9]/.test(val),
                special: /[@$!%*?&]/.test(val)
            };

            for (const key in checks) {
                if (checks[key]) {
                    requirements[key].classList.add('valid');
                    score++;
                } else {
                    requirements[key].classList.remove('valid');
                }
            }

            meter.className = ''; 
            if (val.length === 0) {
                strengthText.innerText = 'Muy débil';
                meter.style.width = '0';
            } else if (score <= 1) {
                strengthText.innerText = 'Débil';
                meter.classList.add('strength-weak');
            } else if (score === 2) {
                strengthText.innerText = 'Media';
                meter.classList.add('strength-medium');
            } else if (score === 3) {
                strengthText.innerText = 'Buena';
                meter.classList.add('strength-good');
            } else if (score === 4) {
                strengthText.innerText = 'Fuerte';
                meter.classList.add('strength-strong');
            }
        });
    }

    let registerForm = document.querySelector('form[action="/auth/store"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let pass = document.querySelector('#password').value;
            let confirmPass = document.querySelector('#confirmPassword').value;

            // Validar que coincidan
            if (pass !== confirmPass) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Las contraseñas no coinciden',
                    text: 'Por favor, asegúrate de que ambas contraseñas sean iguales.',
                    confirmButtonColor: '#004a87',
                    heightAuto: false
                });
                return;
            }

            // Validar requisitos mínimos antes de enviar
            const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passRegex.test(pass)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña débil',
                    text: 'La contraseña no cumple con todos los requisitos de seguridad.',
                    confirmButtonColor: '#004a87',
                    heightAuto: false
                });
            }
        });
    }

    let flashData = document.getElementById('flash-data');
    if (flashData) {
        let type = flashData.dataset.type;
        let msg = flashData.dataset.msg;

        Swal.fire({
            icon: type,
            title: type === 'success' ? '¡Hecho!' : 'Atención',
            text: msg,
            confirmButtonColor: '#004a87',
            heightAuto: false,
        }).then((result) => {
            if (type === 'success') {
                if (msg.includes('Registro completado')) {
                    window.location.href = '/auth/index';
                } else {
                    window.location.href = '/client/index';
                }
            }
        });
    }
});