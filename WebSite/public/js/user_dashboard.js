document.addEventListener('DOMContentLoaded', () => {
    initLocationSelects();
    alertMessages();
});

async function initLocationSelects() {
    // Referencias a los elementos del DOM (IDs actualizados según tu HTML)
    const countrySelect = document.getElementById('country-select') || document.getElementById('pais');
    const stateSelect   = document.getElementById('state-select')   || document.getElementById('provincia');
    const citySelect    = document.getElementById('city-select')    || document.getElementById('ciudad');
    const zipInput      = document.querySelector('input[name="postalCode"]');

    if (!countrySelect || !stateSelect || !citySelect) return;

    let locationData = [];

    // 1. Cargar el JSON local (Usa la ruta correcta a tu archivo)
    try {
        const response = await fetch('/json/countries.json'); 
        const json = await response.json();
        locationData = json.data; // Tu JSON tiene la propiedad "data"

        // Poblar Países inicialmente
        countrySelect.innerHTML = '<option value="">Seleccione País</option>';
        locationData.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.country;
            opt.textContent = item.country;
            // Mantener selección si existe data-value (útil en edición de perfil)
            if (countrySelect.dataset.value === item.country) opt.selected = true;
            countrySelect.appendChild(opt);
        });

        // Si ya hay un país seleccionado por defecto, cargar sus datos
        if (countrySelect.value) updateCities(countrySelect.value);

    } catch (e) {
        console.error("Error al cargar el archivo de países local:", e);
    }

    // 2. Función para manejar el cambio de País
    // Nota: Como tu JSON actual no tiene "provincias", cargamos ciudades directamente
    function updateCities(countryName) {
        citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        citySelect.disabled = true;

        const countryMatch = locationData.find(c => c.country === countryName);

        if (countryMatch && countryMatch.cities) {
            citySelect.innerHTML = '<option value="">Seleccione Ciudad</option>';
            countryMatch.cities.forEach(cityName => {
                const opt = document.createElement('option');
                opt.value = cityName;
                opt.textContent = cityName;
                if (citySelect.dataset.value === cityName) opt.selected = true;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;

            // Parche visual para Provincia: Como no hay en el JSON, ponemos el país o "N/A"
            stateSelect.innerHTML = `<option value="${countryName}">${countryName}</option>`;
            stateSelect.disabled = false;
        } else {
            citySelect.innerHTML = '<option value="">Sin ciudades</option>';
            citySelect.disabled = true;
        }
    }

    // --- Listeners de Eventos ---

    countrySelect.addEventListener('change', function() {
        // Resetear selects dependientes y dataset
        stateSelect.dataset.value = '';
        citySelect.dataset.value = '';
        if (zipInput) zipInput.value = ''; 
        
        updateCities(this.value);
    });

    // Listener para el Código Postal
    citySelect.addEventListener('change', function() {
        // Si en el futuro tu JSON tiene CPs (ej: {name: "Madrid", zip: "28001"}),
        // aquí podrías auto-rellenarlo. Por ahora lo dejamos para entrada manual.
        if (zipInput) {
            zipInput.placeholder = "Escribe el código postal";
        }
    });
}

// Función de Alertas (SweetAlert)
function alertMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        Swal.fire({
            icon: 'success',
            title: '¡Completado!',
            text: 'La operación se ha realizado con éxito.',
            confirmButtonColor: '#18507F',
            timer: 3000
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    if (urlParams.has('error')) {
        let msg = 'Hubo un error al procesar la solicitud.';
        const type = urlParams.get('error');
        if (type === 'fields') msg = 'Por favor, rellena todos los campos obligatorios.';
        if (type === 'db') msg = 'Error de conexión con la base de datos.';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#18507F'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}