document.addEventListener('DOMContentLoaded', () => {
    initLocationSelects();
    alertMessages();
});

function initLocationSelects() {
    const countrySelect = document.getElementById('pais');
    const stateSelect   = document.getElementById('provincia');
    const citySelect    = document.getElementById('ciudad');

    if (!countrySelect || !stateSelect || !citySelect) return;

    const apiBase = "https://countriesnow.space/api/v0.1/countries";

    // 1. Cargar países vía API
    fetch(apiBase)
        .then(res => res.json())
        .then(data => {
            countrySelect.innerHTML = '<option value="">Seleccione País</option>';
            data.data.forEach(country => {
                const opt = document.createElement('option');
                opt.value = country.country;
                opt.textContent = country.country;
                if (countrySelect.dataset.value === country.country) opt.selected = true;
                countrySelect.appendChild(opt);
            });
            if (countrySelect.value) loadProvinces(countrySelect.value);
        })
        .catch(console.error);

    // 2. Función para cargar provincias
    const loadProvinces = async (country) => {
        stateSelect.innerHTML = '<option value="">Cargando provincias...</option>';
        stateSelect.disabled = true;
        citySelect.innerHTML  = '<option value="">Seleccione provincia primero</option>';
        citySelect.disabled   = true;

        if (!country) {
            stateSelect.innerHTML = '<option value="">—</option>';
            return;
        }

        try {
            const response = await fetch(`${apiBase}/states`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ country })
            });
            const resData = await response.json();

            let states = resData.data?.states?.map(s => s.name) || [];

            // 🔥 Patch España: Ceuta y Melilla
            if (country === 'Spain') {
                const spainStates = [
                    "Andalucía","Madrid","Cataluña","Valencia","Galicia",
                    "Castilla y León","País Vasco","Canarias","Baleares",
                    "Murcia","Castilla-La Mancha","Aragón","Extremadura",
                    "Navarra","La Rioja","Cantabria","Asturias","Ceuta","Melilla"
                ];
                states = spainStates;
            }

            stateSelect.innerHTML = '<option value="">Seleccione Provincia/Estado</option>';
            states.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                if (stateSelect.dataset.value === s) opt.selected = true;
                stateSelect.appendChild(opt);
            });
            stateSelect.disabled = false;

            if (stateSelect.dataset.value) loadCities(country, stateSelect.dataset.value);

        } catch (e) {
            console.error("Error cargando provincias:", e);
            stateSelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    };

    // 3. Función para cargar ciudades vía API, parche Ceuta/Melilla
    const loadCities = async (country, state) => {
        citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        citySelect.disabled = true;

        try {
            let cities = [];

            // Patch para Ceuta y Melilla (la API no devuelve ciudades)
            if (country === 'Spain' && (state === 'Ceuta' || state === 'Melilla')) {
                cities = [state]; // ciudad = nombre de la provincia
            } else {
                const response = await fetch(`${apiBase}/state/cities`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ country, state })
                });
                const resData = await response.json();
                cities = resData.data || [];
            }

            if (!cities.length) {
                citySelect.innerHTML = '<option value="">No disponible</option>';
                return;
            }

            citySelect.innerHTML = '<option value="">Seleccione Ciudad</option>';
            cities.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.textContent = c;
                if (citySelect.dataset.value === c) opt.selected = true;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;

        } catch (e) {
            console.error("Error cargando ciudades:", e);
            citySelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    };

    // 4. Event listeners
    countrySelect.addEventListener('change', function() {
        stateSelect.dataset.value = '';
        citySelect.dataset.value = '';
        loadProvinces(this.value);
    });

    stateSelect.addEventListener('change', function() {
        citySelect.dataset.value = '';
        loadCities(countrySelect.value, this.value);
    });
}

// Alertas de éxito o error
function alertMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: 'Los detalles de tu cuenta se han guardado correctamente.',
            confirmButtonColor: '#18507F',
            timer: 3000
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    if (urlParams.has('error')) {
        let mensaje = 'Hubo un problema al procesar la solicitud.';
        const errorType = urlParams.get('error');
        if (errorType === 'fields') mensaje = 'Por favor, rellena los campos obligatorios.';
        if (errorType === 'db') mensaje = 'Error de base de datos. Revisa los nombres de las columnas.';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonColor: '#18507F'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}