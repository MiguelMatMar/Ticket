// public/js/alerts.js
document.addEventListener('DOMContentLoaded', () => {
    // Buscamos si existe el elemento que trae el mensaje del servidor
    let flashData = document.getElementById('flash-message');
    
    if (flashData) {
        Swal.fire({
            icon: flashData.dataset.type,
            title: flashData.dataset.msg,
            confirmButtonText: 'Aceptar'
        });
    }
});