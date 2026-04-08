document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('replyMessage')) {
        initReplyEditor('#replyMessage');
    }

    let replyContainer = document.getElementById('replyFormContainer');
    let btnOpen = document.getElementById('btnOpenReply');
    let btnClose = document.getElementById('btnCloseReply');

    let toggleForm = () => {
        let isHidden = replyContainer.style.display === 'none';
        replyContainer.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            replyContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    if (btnOpen) btnOpen.addEventListener('click', toggleForm);
    if (btnClose) btnClose.addEventListener('click', () => replyContainer.style.display = 'none');

    let btnAddFile = document.getElementById('addFileReply');
    if (btnAddFile) {
        btnAddFile.addEventListener('click', (e) => {
            e.preventDefault();
            let container = document.getElementById('inputsAdjuntos');
            let wrapper = document.createElement('div');
            wrapper.style.marginTop = "10px";
            let input = document.createElement('input');
            input.type = "file";
            input.name = "fileUsers[]";
            input.className = "file-input";
            wrapper.appendChild(input);
            container.appendChild(wrapper);
        });
    }

    let formReply = document.getElementById('formReplyTicket');
    if (formReply) {
        formReply.addEventListener('submit', function(e) {
            let allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'zip', 'sql'];
            let files = formReply.querySelectorAll('input[name="fileUsers[]"]');
            let isValid = true;

            files.forEach(input => {
                if (input.files.length > 0) {
                    let ext = input.files[0].name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(ext)) isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no permitido',
                    text: 'Formatos válidos: ' + allowedExtensions.join(', ')
                });
                return;
            }

            Swal.fire({
                title: 'Enviando respuesta...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        });
    }
});

function initReplyEditor(selector) {
    if ($(selector).length > 0) {
        $(selector).summernote({
            placeholder: 'Escribe tu respuesta detallada aquí...',
            tabsize: 2,
            height: 200,
            lang: 'es-ES',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onChange: function(contents) {
                    $(selector).val(contents);
                }
            }
        });
    }
}

function scrollToResponse() {
    let container = document.getElementById('replyFormContainer');
    if (container) {
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}