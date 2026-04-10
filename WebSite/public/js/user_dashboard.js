document.addEventListener('DOMContentLoaded', () => {
    initProfileAddress();
    initWhatsappSync();
    alertMessages();
});


// =============================================================================
// 1. DIRECCIÓN — PAÍS + CP → AUTOCOMPLETA PROVINCIA / CIUDAD (zippopotam.us)
// =============================================================================
function initProfileAddress() {

    const countrySelect  = document.getElementById('profile-country-select');
    const postalInput    = document.getElementById('profile-postal-input');
    const countryNameInput = document.getElementById('profile-country-name');
    const stateInput     = document.getElementById('profile-state-input');
    const cityInput      = document.getElementById('profile-city-input');
    const postalSpinner  = document.getElementById('profile-postal-spinner');
    const postalOk       = document.getElementById('profile-postal-ok');
    const postalError    = document.getElementById('profile-postal-error');
    const postalHint     = document.getElementById('profile-postal-hint');

    if (!countrySelect || !postalInput) return;

    // ── Países soportados por zippopotam.us ──────────────────────────────────
    const SUPPORTED_COUNTRIES = {
        'ad': 'Andorra',         'ar': 'Argentina',       'at': 'Austria',
        'au': 'Australia',       'be': 'Bélgica',          'bg': 'Bulgaria',
        'br': 'Brasil',          'ca': 'Canadá',           'ch': 'Suiza',
        'cz': 'República Checa', 'de': 'Alemania',         'dk': 'Dinamarca',
        'do': 'Rep. Dominicana', 'es': 'España',           'fi': 'Finlandia',
        'fo': 'Islas Feroe',     'fr': 'Francia',          'gb': 'Reino Unido',
        'gg': 'Guernsey',        'gl': 'Groenlandia',      'gp': 'Guadalupe',
        'gt': 'Guatemala',       'gu': 'Guam',             'hr': 'Croacia',
        'hu': 'Hungría',         'ie': 'Irlanda',          'im': 'Isla de Man',
        'in': 'India',           'is': 'Islandia',         'it': 'Italia',
        'je': 'Jersey',          'jp': 'Japón',            'li': 'Liechtenstein',
        'lt': 'Lituania',        'lu': 'Luxemburgo',       'lv': 'Letonia',
        'mc': 'Mónaco',          'md': 'Moldavia',         'me': 'Montenegro',
        'mh': 'Islas Marshall',  'mk': 'Macedonia',        'mp': 'Marianas del Norte',
        'mq': 'Martinica',       'mx': 'México',           'my': 'Malasia',
        'nl': 'Países Bajos',    'no': 'Noruega',          'nz': 'Nueva Zelanda',
        'ph': 'Filipinas',       'pk': 'Pakistán',         'pl': 'Polonia',
        'pm': 'San Pedro y Miq.','pr': 'Puerto Rico',      'pt': 'Portugal',
        're': 'Reunión',         'ro': 'Rumanía',          'ru': 'Rusia',
        'se': 'Suecia',          'si': 'Eslovenia',        'sj': 'Svalbard',
        'sk': 'Eslovaquia',      'sm': 'San Marino',       'th': 'Tailandia',
        'tr': 'Turquía',         'us': 'Estados Unidos',   'va': 'Vaticano',
        'vi': 'Islas Vírgenes',  'za': 'Sudáfrica'
    };

    // ── Prefijos CP → provincia (España) ─────────────────────────────────────
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

    // ── Poblar el select con los países soportados (España ya está fija en HTML) ──
    // El nombre guardado en BD viene del hidden #profile-country-name
    const savedCountryName = countryNameInput ? countryNameInput.value.trim() : '';

    Object.entries(SUPPORTED_COUNTRIES)
        .filter(([code]) => code !== 'es')
        .sort(([, a], [, b]) => a.localeCompare(b, 'es'))
        .forEach(([code, name]) => {
            const opt = new Option(name, code);
            opt.dataset.name = name;
            // Seleccionar la opción que coincida con el nombre guardado en BD
            if (savedCountryName && savedCountryName === name) opt.selected = true;
            countrySelect.add(opt);
        });

    // ── Restaurar estado inicial si el usuario ya tenía país y CP guardados ──
    if (countrySelect.value) {
        postalInput.disabled    = false;
        postalInput.placeholder = 'Ej: 28001';
        if (postalHint) {
            postalHint.textContent = postalInput.value.trim()
                ? 'Puedes cambiar el código postal si lo necesitas.'
                : 'Escribe tu código postal y rellenaremos el resto.';
            postalHint.classList.remove('is-error');
        }
        // Marcar provincia y ciudad como filled si ya tienen valor
        if (stateInput && stateInput.value.trim()) stateInput.classList.add('filled');
        if (cityInput  && cityInput.value.trim())  cityInput.classList.add('filled');
    }

    // ── Helpers de iconos ─────────────────────────────────────────────────────
    function setPostalIcon(icon) {
        [postalSpinner, postalOk, postalError].forEach(el => el && el.classList.remove('visible'));
        if (icon) icon.classList.add('visible');
    }

    function clearLocation() {
        if (stateInput) { stateInput.value = ''; stateInput.classList.remove('filled'); }
        if (cityInput)  { cityInput.value  = ''; cityInput.classList.remove('filled'); }
        setPostalIcon(null);
    }

    function fillLocation(state, city) {
        if (stateInput) { stateInput.value = state; stateInput.classList.add('filled'); }
        if (cityInput)  { cityInput.value  = city;  cityInput.classList.add('filled'); }
    }

    function handleResult(data, countryCode, cp) {
        let state      = data['places']?.[0]?.['state']       || '';
        const city     = data['places']?.[0]?.['place name']  || '';

        // Fallback provincia España por prefijo de CP
        if (countryCode === 'es' && !state && cp) {
            state = ES_PROVINCES[cp.substring(0, 2)] || '';
        }

        if (!city && !state) {
            setPostalIcon(postalError);
            if (postalHint) {
                postalHint.textContent = 'No se encontraron datos para este código postal.';
                postalHint.classList.add('is-error');
            }
            return;
        }

        fillLocation(state, city);
        setPostalIcon(postalOk);
        if (postalHint) {
            postalHint.textContent = '¡Listo! Datos completados automáticamente.';
            postalHint.classList.remove('is-error');
        }
    }

    // ── Cambio de país ────────────────────────────────────────────────────────
    countrySelect.addEventListener('change', function () {
        const code = this.value;
        clearLocation();
        postalInput.value = '';

        if (code) {
            postalInput.disabled    = false;
            postalInput.placeholder = 'Ej: 28001';
            if (postalHint) {
                postalHint.textContent = 'Escribe tu código postal y rellenaremos el resto.';
                postalHint.classList.remove('is-error');
            }
            // Guardar nombre legible del país
            const selectedOpt = this.options[this.selectedIndex];
            if (countryNameInput) {
                countryNameInput.value = selectedOpt.dataset.name || selectedOpt.text.replace(/^.+\s/, '');
            }
            postalInput.focus();
        } else {
            postalInput.disabled    = true;
            postalInput.placeholder = 'Selecciona país primero';
            if (postalHint) {
                postalHint.textContent = 'Primero selecciona el país.';
                postalHint.classList.remove('is-error');
            }
            if (countryNameInput) countryNameInput.value = '';
        }
    });

    // ── Escritura de CP con debounce ──────────────────────────────────────────
    let debounceTimer = null;

    postalInput.addEventListener('input', function () {
        const cp          = this.value.trim();
        const countryCode = countrySelect.value;

        clearTimeout(debounceTimer);
        clearLocation();
        if (postalHint) {
            postalHint.textContent = 'Escribe tu código postal y rellenaremos el resto.';
            postalHint.classList.remove('is-error');
        }

        if (cp.length < 3 || !countryCode) return;

        setPostalIcon(postalSpinner);

        debounceTimer = setTimeout(async () => {
            try {
                const res = await fetch(`https://api.zippopotam.us/${countryCode}/${encodeURIComponent(cp)}`);
                if (!res.ok) throw new Error('not_found');
                handleResult(await res.json(), countryCode, cp);
            } catch (err) {
                setPostalIcon(postalError);
                if (postalHint) {
                    postalHint.textContent = 'Código postal no encontrado. Compruébalo e inténtalo de nuevo.';
                    postalHint.classList.add('is-error');
                }
            }
        }, 600);
    });
}


// =============================================================================
// 2. WHATSAPP "IGUAL AL MÓVIL"
// =============================================================================
function initWhatsappSync() {
    const mobileInput   = document.getElementById('profile-mobile-input');
    const whatsappInput = document.getElementById('profile-whatsapp-input');
    const whatsappSame  = document.getElementById('profile-whatsapp-same');

    if (!mobileInput || !whatsappInput || !whatsappSame) return;

    // Al marcar el checkbox: copia el valor del móvil y bloquea el campo
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

    // Antes de enviar el formulario: re-habilitar WhatsApp si estaba bloqueado
    const form = document.querySelector('form[action="/client/updateProfile"]');
    if (form) {
        form.addEventListener('submit', function () {
            if (whatsappInput.disabled) whatsappInput.disabled = false;
        });
    }
}


// =============================================================================
// 3. ALERTAS SWEETALERT (parámetros en la URL)
// =============================================================================
function alertMessages() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('success')) {
        Swal.fire({
            icon: 'success',
            title: '¡Completado!',
            text: 'Los cambios se han guardado correctamente.',
            confirmButtonColor: '#18507F',
            timer: 3000,
            timerProgressBar: true
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    if (urlParams.has('error')) {
        let msg = 'Hubo un error al procesar la solicitud.';
        const type = urlParams.get('error');
        if (type === 'fields') msg = 'Por favor, rellena todos los campos obligatorios.';
        if (type === 'db')     msg = 'Error de conexión con la base de datos.';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#18507F'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}