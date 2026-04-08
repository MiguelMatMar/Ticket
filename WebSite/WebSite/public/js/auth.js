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

    let registerForm = document.querySelector('form[action="/auth/store"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let pass = document.querySelector('#password').value;
            let confirmPass = document.querySelector('#confirmPassword').value;

            if (pass !== confirmPass) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Las contraseñas no coinciden',
                    text: 'Por favor, asegúrate de que ambas contraseñas sean iguales.',
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
                // Si el mensaje es de registro exitoso, lo mandamos al login
                if (msg.includes('Registro completado')) {
                    window.location.href = '/auth/index';
                } 
                // Si el mensaje es de login exitoso (o cualquier otro éxito), al panel
                else {
                    window.location.href = '/client/index';
                }
            }
        });
    }
});