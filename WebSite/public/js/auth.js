document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. TOGGLE DE CONTRASEÑAS ---
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

    // --- 2. LÓGICA DEL MODAL (LOGIN) ---
    const openModalBtn = document.getElementById('openRegisterModal');
    const typeModal = document.getElementById('typeModal');
    const closeModalBtn = document.getElementById('closeModal');

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            typeModal.style.display = 'flex';
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            typeModal.style.display = 'none';
        });
    }

    window.goToRegister = function(type) {
        window.location.href = `/auth/register?type=${type}`;
    };

    // --- 3. ADAPTACIÓN DEL FORMULARIO SEGÚN TIPO (REGISTER) ---
    const params = new URLSearchParams(window.location.search);
    const userType = params.get('type');
    const companyGroup = document.getElementById('group-company');
    const registerTitle = document.getElementById('register-title');

    if (userType === 'persona') {
        if (companyGroup) companyGroup.style.display = 'none';
        if (registerTitle) registerTitle.innerText = 'Registro Persona Física';
    } else if (userType === 'empresa') {
        if (registerTitle) registerTitle.innerText = 'Registro Empresa';
        const companyInput = document.querySelector('input[name="company"]');
        if (companyInput) companyInput.setAttribute('required', 'required');
    }

    // --- 4. POWERMETER Y REQUISITOS ---
    const passwordInput = document.querySelector('#password');
    const meter = document.querySelector('#strength-meter');
    const strengthText = document.querySelector('#strength-text span');
    const requirements = {
        length: document.querySelector('#req-length'),
        upper: document.querySelector('#req-upper'),
        number: document.querySelector('#req-number'),
        special: document.querySelector('#req-special')
    };

    if (passwordInput && meter) {
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
                if (requirements[key]) {
                    if (checks[key]) {
                        requirements[key].classList.add('valid');
                        score++;
                    } else {
                        requirements[key].classList.remove('valid');
                    }
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

    // --- 5. VALIDACIÓN DE FORMULARIO ---
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
                    confirmButtonColor: '#004a87'
                });
                return;
            }

            const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passRegex.test(pass)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña débil',
                    text: 'La contraseña no cumple con todos los requisitos de seguridad.',
                    confirmButtonColor: '#004a87'
                });
            }
        });
    }

    // --- 6. MENSAJES FLASH ---
    let flashData = document.getElementById('flash-data');
    if (flashData) {
        let type = flashData.dataset.type;
        let msg = flashData.dataset.msg;

        Swal.fire({
            icon: type,
            title: type === 'success' ? '¡Hecho!' : 'Atención',
            text: msg,
            confirmButtonColor: '#004a87'
        }).then(() => {
            if (type === 'success') {
                if (msg.includes('Registro completado')) {
                    window.location.href = '/auth/index';
                } else {
                    window.location.href = '/client/index';
                }
            }
        });
    }

    // --- 7. LOCALIZACIÓN LOCAL  ---
    const countrySelect = document.getElementById('country-select');
    const stateSelect = document.getElementById('state-select');
    const citySelect = document.getElementById('city-select');
    const zipInput = document.querySelector('input[name="postalCode"]');

    if (countrySelect && stateSelect && citySelect) {
        let locationData = [];

        // Cargar el JSON local
        fetch('/json/countries.json')
            .then(res => res.json())
            .then(json => {
                locationData = json.data;
                countrySelect.innerHTML = '<option value="">Seleccione País</option>';
                locationData.forEach(item => {
                    const opt = new Option(item.country, item.country);
                    countrySelect.add(opt);
                });
            })
            .catch(err => console.error("Error cargando JSON local:", err));

        // Evento cambio de País
        countrySelect.addEventListener('change', function() {
            const countryName = this.value;
            citySelect.innerHTML = '<option value="">Seleccione Ciudad</option>';
            citySelect.disabled = true;

            // Como el JSON no tiene provincias, usamos el país como provincia predeterminada
            if (countryName) {
                stateSelect.innerHTML = `<option value="${countryName}">${countryName}</option>`;
                stateSelect.disabled = false;

                const countryMatch = locationData.find(c => c.country === countryName);
                if (countryMatch && countryMatch.cities) {
                    citySelect.innerHTML = '<option value="">Seleccione Ciudad</option>';
                    countryMatch.cities.forEach(city => {
                        citySelect.add(new Option(city, city));
                    });
                    citySelect.disabled = false;
                }
            } else {
                stateSelect.innerHTML = '<option value="">Seleccione País primero</option>';
                stateSelect.disabled = true;
            }
        });

        // Evento cambio de Ciudad (Foco al Código Postal)
        citySelect.addEventListener('change', function() {
            if (zipInput) zipInput.focus();
        });
    }
});