document.addEventListener('DOMContentLoaded',()=>{
    addFile(); // Boton para añadir un archivo extra
    textArea(); // Mejora el texarea
    handleFormSubmit(); // Valida y da respuesta al formulario de abrir ticket
    cancelButton();


    // Mensaje de exito al enviar el formulario

    let urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Tu ticket ha sido creado correctamente.',
            confirmButtonColor: '#007bff'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }

})

function textArea() {
    // Verificamos si el elemento existe antes de inicializar
    if ($('#messageUser').length > 0) {
        $('#messageUser').summernote({
            placeholder: 'Escribe aquí los detalles de tu consulta...',
            tabsize: 2,
            height: 250,
            lang: 'es-ES', // Opcional si tienes el idioma
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            // Esto asegura que el contenido se guarde en el textarea original al escribir
            callbacks: {
                onChange: function(contents, $editable) {
                    $('#messageUser').val(contents);
                }
            }
        });
    }
}



function addFile(){
    let addFileBtn = document.getElementById('addFile');
    addFileBtn.addEventListener('click', (e) => {
        e.preventDefault();
        let contentFiles = document.getElementById('files');
        
        // Creamos un contenedor para cada input
        let wrapper = document.createElement('div');
        wrapper.style.marginTop = "10px";
        
        let newInputFile = document.createElement('input');
        newInputFile.type = "file";
        newInputFile.name = "fileUsers[]";
        
        wrapper.appendChild(newInputFile);
        // Insertamos el wrapper antes del botón
        contentFiles.insertBefore(wrapper, addFileBtn);
    });
}

function handleFormSubmit() {
    let form = document.querySelector('form');
    let allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'zip', 'sql'];
    

    form.addEventListener('submit', function(e) {
        let files = document.querySelectorAll('input[name="fileUsers[]"]');
        let isValid = true;

        files.forEach(input => {
            if (input.files.length > 0) {
                let file = input.files[0];
                let extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Archivo no permitido',
                text: 'Solo se permiten: ' + allowedExtensions.join(', ')
            });
            return;
        }

        // Si es válido, mostramos la alerta de carga y dejamos que el formulario se envíe
        Swal.fire({
            title: 'Creando ticket...',
            text: 'Por favor, espera un momento.',
            allowOutsideClick: false,
            didOpen: () => { 
                Swal.showLoading(); 
            }
        });
        
    });
}

function cancelButton(){
    let button = document.getElementsByClassName('btn-cancel')[0];
    if (button) {
        button.addEventListener('click', () => {
            window.location.href = '/support/tickets';
        });
    }
}