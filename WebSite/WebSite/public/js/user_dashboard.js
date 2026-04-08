

function updateUsrBtn(){
    let updateAccBtn = document.getElementById('updateAccBtn');

    if(updateAccBtn){
        updateAccBtn.addEventListener('click', () => {
            window.location.href = '/client/accdetails';
        });
    }
    
}
function alert(){
    let urlParams = new URLSearchParams(window.location.search);

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
        if (urlParams.get('error') === 'fields') mensaje = 'Por favor, rellena los campos obligatorios.';
        if (urlParams.get('error') === 'db') mensaje = 'Error de base de datos. Revisa los nombres de las columnas.';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonColor: '#18507F'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}