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
    const typeModal    = document.getElementById('typeModal');
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
    const params       = new URLSearchParams(window.location.search);
    const userType     = params.get('type');   // 'persona' | 'empresa' | null
    const isEmpresa    = userType === 'empresa';

    // Elementos que pueden existir en la página de registro
    const registerTitle = document.getElementById('register-title');
    const companyGroup  = document.getElementById('group-company');
    const companyInput  = document.querySelector('input[name="company"]');
    const labelNif      = document.getElementById('label-nif');
    const inputNif      = document.getElementById('input-nif');
    const inputType     = document.getElementById('input-type');
    const btnPersona    = document.getElementById('btn-persona');
    const btnEmpresa    = document.getElementById('btn-empresa');

    // Solo ejecutamos si estamos en la página de registro
    if (companyGroup || labelNif) {

        function applyType(empresa) {
            if (empresa) {
                // --- Empresa ---
                if (registerTitle) registerTitle.innerText = 'Registro Empresa';

                if (companyGroup)  companyGroup.style.display = '';
                if (companyInput)  companyInput.setAttribute('required', 'required');

                if (labelNif)  labelNif.textContent  = 'Nº NIF/CIF';
                if (inputNif)  inputNif.placeholder  = 'NIF / CIF';
                if (inputType) inputType.value       = 'empresa';

                if (btnEmpresa) btnEmpresa.classList.add('active');
                if (btnPersona) btnPersona.classList.remove('active');

            } else {
                // --- Persona física ---
                if (registerTitle) registerTitle.innerText = 'Registro Persona Física';

                if (companyGroup)  companyGroup.style.display = 'none';
                if (companyInput) {
                    companyInput.removeAttribute('required');
                    companyInput.value = '';
                }

                if (labelNif)  labelNif.textContent  = 'DNI';
                if (inputNif)  inputNif.placeholder  = 'DNI';
                if (inputType) inputType.value       = 'persona';

                if (btnPersona) btnPersona.classList.add('active');
                if (btnEmpresa) btnEmpresa.classList.remove('active');
            }
        }

        // Aplicar estado inicial según la URL
        applyType(isEmpresa);
    }


    // --- 4. MEDIDOR DE FORTALEZA DE CONTRASEÑA ---
    const passwordInput = document.querySelector('#password');
    const meter         = document.querySelector('#strength-meter');
    const strengthText  = document.querySelector('#strength-text span');
    const requirements  = {
        length:  document.querySelector('#req-length'),
        upper:   document.querySelector('#req-upper'),
        number:  document.querySelector('#req-number'),
        special: document.querySelector('#req-special')
    };

    if (passwordInput && meter) {
        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let score = 0;

            const checks = {
                length:  val.length >= 8,
                upper:   /[A-Z]/.test(val),
                number:  /[0-9]/.test(val),
                special: /[@$!%*?&]/.test(val)
            };

            for (const key in checks) {
                if (requirements[key]) {
                    requirements[key].classList.toggle('valid', checks[key]);
                    if (checks[key]) score++;
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


    // --- 5. VALIDACIÓN DEL FORMULARIO ---
    const registerForm = document.querySelector('form[action="/auth/store"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // Re-habilitar el campo WhatsApp si estaba bloqueado por el checkbox,
            // para que su valor se incluya en el POST
            const wa = document.getElementById('whatsapp-input');
            if (wa && wa.disabled) wa.disabled = false;

            const pass        = document.querySelector('#password').value;
            const confirmPass = document.querySelector('#confirmPassword').value;

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
    const flashData = document.getElementById('flash-data');
    if (flashData) {
        const type = flashData.dataset.type;
        const msg  = flashData.dataset.msg;

        Swal.fire({
            icon: type,
            title: type === 'success' ? '¡Hecho!' : 'Atención',
            text: msg,
            confirmButtonColor: '#004a87'
        }).then(() => {
            if (type === 'success') {
                window.location.href = msg.includes('Registro completado')
                    ? '/auth/index'
                    : '/client/index';
            }
        });
    }


    // --- 7. CÓDIGO POSTAL → AUTOCOMPLETAR PROVINCIA / CIUDAD ---
    const countryCodeSelect = document.getElementById('country-code-select');
    const postalInput       = document.getElementById('postal-input');
    const countryInput      = document.getElementById('country-input');
    const stateInput        = document.getElementById('state-input');
    const cityInput         = document.getElementById('city-input');
    const postalSpinner     = document.getElementById('postal-spinner');
    const postalOk          = document.getElementById('postal-ok');
    const postalError       = document.getElementById('postal-error');
    const postalHint        = document.getElementById('postal-hint');

    // Países soportados por zippopotam.us con su código ISO2
    // https://www.zippopotam.us/#where
    const SUPPORTED_COUNTRIES = {
        'ad': 'Andorra',        'ar': 'Argentina',      'at': 'Austria',
        'au': 'Australia',      'be': 'Bélgica',         'bg': 'Bulgaria',
        'br': 'Brasil',         'ca': 'Canadá',          'ch': 'Suiza',
        'cz': 'República Checa','de': 'Alemania',        'dk': 'Dinamarca',
        'do': 'Rep. Dominicana','es': 'España',          'fi': 'Finlandia',
        'fo': 'Islas Feroe',    'fr': 'Francia',         'gb': 'Reino Unido',
        'gg': 'Guernsey',       'gl': 'Groenlandia',     'gp': 'Guadalupe',
        'gt': 'Guatemala',      'gu': 'Guam',            'hr': 'Croacia',
        'hu': 'Hungría',        'ie': 'Irlanda',         'im': 'Isla de Man',
        'in': 'India',          'is': 'Islandia',        'it': 'Italia',
        'je': 'Jersey',         'jp': 'Japón',           'li': 'Liechtenstein',
        'lt': 'Lituania',       'lu': 'Luxemburgo',      'lv': 'Letonia',
        'mc': 'Mónaco',         'md': 'Moldavia',        'me': 'Montenegro',
        'mh': 'Islas Marshall', 'mk': 'Macedonia',       'mp': 'Marianas del Norte',
        'mq': 'Martinica',      'mx': 'México',          'my': 'Malasia',
        'nl': 'Países Bajos',   'no': 'Noruega',         'nz': 'Nueva Zelanda',
        'ph': 'Filipinas',      'pk': 'Pakistán',        'pl': 'Polonia',
        'pm': 'San Pedro y Miq.','pr': 'Puerto Rico',    'pt': 'Portugal',
        're': 'Reunión',        'ro': 'Rumanía',         'ru': 'Rusia',
        'se': 'Suecia',         'si': 'Eslovenia',       'sj': 'Svalbard',
        'sk': 'Eslovaquia',     'sm': 'San Marino',      'th': 'Tailandia',
        'tr': 'Turquía',        'us': 'Estados Unidos',  'va': 'Vaticano',
        'vi': 'Islas Vírgenes', 'za': 'Sudáfrica'
    };

    // Mapa de prefijos de CP español → provincia
    const ES_PROVINCES = {
        '01': 'Álava',          '02': 'Albacete',       '03': 'Alicante',
        '04': 'Almería',        '05': 'Ávila',          '06': 'Badajoz',
        '07': 'Islas Baleares', '08': 'Barcelona',      '09': 'Burgos',
        '10': 'Cáceres',        '11': 'Cádiz',          '12': 'Castellón',
        '13': 'Ciudad Real',    '14': 'Córdoba',        '15': 'A Coruña',
        '16': 'Cuenca',         '17': 'Girona',         '18': 'Granada',
        '19': 'Guadalajara',    '20': 'Gipuzkoa',       '21': 'Huelva',
        '22': 'Huesca',         '23': 'Jaén',           '24': 'León',
        '25': 'Lleida',         '26': 'La Rioja',       '27': 'Lugo',
        '28': 'Madrid',         '29': 'Málaga',         '30': 'Murcia',
        '31': 'Navarra',        '32': 'Ourense',        '33': 'Asturias',
        '34': 'Palencia',       '35': 'Las Palmas',     '36': 'Pontevedra',
        '37': 'Salamanca',      '38': 'Santa Cruz de Tenerife', '39': 'Cantabria',
        '40': 'Segovia',        '41': 'Sevilla',        '42': 'Soria',
        '43': 'Tarragona',      '44': 'Teruel',         '45': 'Toledo',
        '46': 'Valencia',       '47': 'Valladolid',     '48': 'Bizkaia',
        '49': 'Zamora',         '50': 'Zaragoza',       '51': 'Ceuta',
        '52': 'Melilla'
    };

    if (countryCodeSelect && postalInput && stateInput && cityInput) {

        // Poblar el select con los países soportados (España ya está fija arriba)
        Object.entries(SUPPORTED_COUNTRIES)
            .filter(([code]) => code !== 'es')
            .sort(([, a], [, b]) => a.localeCompare(b, 'es'))
            .forEach(([code, name]) => {
                const opt = new Option(name, code);
                opt.dataset.name = name;
                countryCodeSelect.add(opt);
            });

        function setPostalIcon(icon) {
            [postalSpinner, postalOk, postalError].forEach(el => el.classList.remove('visible'));
            if (icon) icon.classList.add('visible');
        }

        function clearLocation() {
            [stateInput, cityInput].forEach(el => {
                el.value = '';
                el.classList.remove('filled');
            });
            setPostalIcon(null);
        }

        function fillLocation(state, city) {
            stateInput.value = state;
            cityInput.value  = city;
            [stateInput, cityInput].forEach(el => el.classList.add('filled'));
        }

        function handleResult(data, countryCode, cp) {
            let state = data['places']?.[0]?.['state']       || '';
            const city  = data['places']?.[0]?.['place name'] || '';

            // Fallback de provincia para España por prefijo de CP
            if (countryCode === 'es' && !state && cp) {
                state = ES_PROVINCES[cp.substring(0, 2)] || '';
            }

            if (!city && !state) {
                setPostalIcon(postalError);
                postalHint.textContent = 'No se encontraron datos para este código postal.';
                postalHint.classList.add('is-error');
                return;
            }

            fillLocation(state, city);
            setPostalIcon(postalOk);
            postalHint.textContent = '¡Listo! Datos completados automáticamente.';
            postalHint.classList.remove('is-error');
        }

        // Al cambiar el país: habilitar el CP y guardar el nombre del país
        countryCodeSelect.addEventListener('change', function () {
            const code = this.value;
            clearLocation();
            postalInput.value = '';

            if (code) {
                postalInput.disabled    = false;
                postalInput.placeholder = 'Ej: 28001';
                postalHint.textContent  = 'Escribe tu código postal y rellenaremos el resto.';
                postalHint.classList.remove('is-error');
                // Guardar el nombre del país en el hidden input
                const selectedOption = this.options[this.selectedIndex];
                if (countryInput) countryInput.value = selectedOption.dataset.name || selectedOption.text.replace(/^.+\s/, '');
                postalInput.focus();
            } else {
                postalInput.disabled    = true;
                postalInput.placeholder = 'Selecciona país primero';
                postalHint.textContent  = 'Primero selecciona el país.';
                if (countryInput) countryInput.value = '';
            }
        });

        // Al escribir el CP: buscar en la API
        let debounceTimer = null;

        postalInput.addEventListener('input', function () {
            const cp          = this.value.trim();
            const countryCode = countryCodeSelect.value;

            clearTimeout(debounceTimer);
            clearLocation();
            postalHint.textContent = 'Escribe tu código postal y rellenaremos el resto.';
            postalHint.classList.remove('is-error');

            if (cp.length < 3 || !countryCode) return;

            setPostalIcon(postalSpinner);

            debounceTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`https://api.zippopotam.us/${countryCode}/${encodeURIComponent(cp)}`);
                    if (!res.ok) throw new Error('not_found');
                    handleResult(await res.json(), countryCode, cp);
                } catch (err) {
                    setPostalIcon(postalError);
                    postalHint.textContent = 'Código postal no encontrado. Compruébalo e inténtalo de nuevo.';
                    postalHint.classList.add('is-error');
                }
            }, 600);
        });
    }

    // --- 8. WHATSAPP "IGUAL AL MÓVIL" ---
    const mobileInput   = document.querySelector('input[name="mobilePhone"]');
    const whatsappInput = document.getElementById('whatsapp-input');
    const whatsappSame  = document.getElementById('whatsapp-same');

    if (mobileInput && whatsappInput && whatsappSame) {
        // Al marcar el checkbox: copia el valor y bloquea el campo
        whatsappSame.addEventListener('change', function () {
            if (this.checked) {
                whatsappInput.value    = mobileInput.value;
                whatsappInput.disabled = true;
            } else {
                whatsappInput.disabled = false;
                whatsappInput.focus();
            }
        });

        // Si el checkbox está marcado y cambia el móvil, sincroniza WhatsApp
        mobileInput.addEventListener('input', function () {
            if (whatsappSame.checked) {
                whatsappInput.value = this.value;
            }
        });
    }

}); 