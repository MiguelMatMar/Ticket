// ── Sistema de trabajo ────────────────────────────────────────────────────────

let mySession      = null;
let chronoInterval = null;

function formatTime(secs) {
    secs = Math.max(0, Math.floor(secs));
    const h = String(Math.floor(secs / 3600)).padStart(2, '0');
    const m = String(Math.floor((secs % 3600) / 60)).padStart(2, '0');
    const s = String(secs % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function startChrono(initialSecs) {
    clearInterval(chronoInterval);
    let secs = Math.max(0, Math.floor(initialSecs));
    document.getElementById('work-chrono').textContent = formatTime(secs);
    document.getElementById('work-chrono-row').style.display = '';
    chronoInterval = setInterval(() => {
        secs++;
        document.getElementById('work-chrono').textContent = formatTime(secs);
    }, 1000);
}

function stopChrono() {
    clearInterval(chronoInterval);
    chronoInterval = null;
    const row = document.getElementById('work-chrono-row');
    if (row) row.style.display = 'none';
}

function setMsg(msg) {
    const el = document.getElementById('work-status-msg');
    if (el) el.textContent = msg;
}

function post(url, data) {
    const form = new FormData();
    for (const [k, v] of Object.entries(data)) form.append(k, v);
    return fetch(url, { method: 'POST', body: form }).then(r => r.json());
}

function makeBtn(cls, icon, label, handler) {
    const btn = document.createElement('button');
    btn.className = cls;
    btn.innerHTML = `<i class="fa-solid ${icon}"></i> ${label}`;
    if (handler) btn.onclick = handler;
    return btn;
}

function renderButtons(state) {
    const box = document.getElementById('work-buttons');
    if (!box) return;
    box.innerHTML = '';

    if (IS_CLOSED) {
        setMsg('El ticket está cerrado.');
        stopChrono();
        return;
    }

    if (state.ronda_finished) {
        setMsg('El trabajo de esta ronda ha sido finalizado.');
        stopChrono();
        return;
    }

    const my = state.my_session;

    if (my && my.estado === 'working') {
        startChrono(state.elapsed_secs);
        box.appendChild(makeBtn('btn-work-pause',  'fa-pause', 'PAUSAR',            handlePause));
        box.appendChild(makeBtn('btn-work-finish', 'fa-stop',  'FINALIZAR TRABAJO',  handleFinish));
        setMsg('Estás trabajando en este ticket.');
        return;
    }

    if (my && my.estado === 'paused') {
        stopChrono();
        if (state.someone_else_working) {
            const quien = state.working_session.nombre + ' ' + state.working_session.apellidos;
            setMsg(`${quien} está trabajando ahora. Espera a que pause para reanudar.`);
            const btnResume = makeBtn('btn-work-start', 'fa-play', 'REANUDAR', null);
            btnResume.disabled = true;
            btnResume.style.opacity = '0.5';
            box.appendChild(btnResume);
        } else {
            box.appendChild(makeBtn('btn-work-start', 'fa-play', 'REANUDAR', handleResume));
            setMsg('Tu trabajo está pausado. Puedes reanudar.');
        }
        box.appendChild(makeBtn('btn-work-finish', 'fa-stop', 'FINALIZAR TRABAJO', handleFinish));
        return;
    }

    stopChrono();

    if (state.someone_else_working) {
        const quien = state.working_session.nombre + ' ' + state.working_session.apellidos;
        setMsg(`${quien} está trabajando en este ticket. Debe pausar para que puedas entrar.`);
        return;
    }

    box.appendChild(makeBtn('btn-work-start', 'fa-play', 'INICIAR TRABAJO', handleStart));
    setMsg('');
}

async function loadStatus() {
    const res  = await fetch(`/worksession/status?ticket_id=${TICKET_ID}`);
    const data = await res.json();

    mySession = data.my_session;

    const totalRonda = document.getElementById('work-total-ronda');
    if (totalRonda) totalRonda.textContent = formatTime(data.total_ronda_secs);

    renderButtons(data);
    loadHistory();
}

async function loadHistory() {
    const res  = await fetch(`/worksession/history?ticket_id=${TICKET_ID}`);
    const data = await res.json();

    const totalGlobal = document.getElementById('work-total-global');
    if (totalGlobal) totalGlobal.textContent = formatTime(data.total_global);

    const box = document.getElementById('work-history-content');
    if (!box) return;

    if (!data.sessions || data.sessions.length === 0) {
        box.textContent = 'Sin sesiones registradas aún.';
        return;
    }

    let html = '';
    let lastRonda = null;
    for (const s of data.sessions) {
        if (s.ronda !== lastRonda) {
            html += `<p style="margin:8px 0 4px;font-weight:bold;color:#004a87;">Ronda ${s.ronda}</p>`;
            lastRonda = s.ronda;
        }
        const tecnico  = s.nombre + ' ' + s.apellidos;
        const inicio   = s.started_at ? s.started_at.substring(0, 16) : '—';
        const fin      = s.finished_at ? s.finished_at.substring(0, 16)
                       : (s.estado === 'paused' ? 'Pausada' : 'En curso');
        const duracion = formatTime(parseInt(s.total_segundos));
        const badge    = s.estado === 'finished' ? '✅' : (s.estado === 'paused' ? '⏸' : '▶️');
        html += `<div style="border-left:3px solid #004a87;padding:4px 8px;margin-bottom:6px;font-size:12px;">
                    ${badge} <strong>${tecnico}</strong><br>
                    Inicio: ${inicio}<br>
                    Fin: ${fin}<br>
                    Duración acumulada: ${duracion}
                 </div>`;
    }
    box.innerHTML = html;
}

async function handleStart() {
    const data = await post('/worksession/start', { ticket_id: TICKET_ID });
    if (data.success) { await loadStatus(); }
    else { alert(data.error || 'No se pudo iniciar el trabajo.'); }
}

async function handlePause() {
    if (!mySession) return;
    const data = await post('/worksession/pause', { ticket_id: TICKET_ID, session_id: mySession.id });
    if (data.success) { stopChrono(); await loadStatus(); }
    else { alert(data.error || 'No se pudo pausar.'); }
}

async function handleResume() {
    const data = await post('/worksession/start', { ticket_id: TICKET_ID });
    if (data.success) { await loadStatus(); }
    else { alert(data.error || 'No se pudo reanudar.'); }
}

async function handleFinish() {
    if (!mySession) return;

    const result = await Swal.fire({
        title: '¿Finalizar trabajo?',
        text: 'Esta acción no se puede deshacer. El tiempo quedará registrado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    const data = await post('/worksession/finish', { ticket_id: TICKET_ID, session_id: mySession.id });
    if (data.success) {
        stopChrono();
        await loadStatus();
        Swal.fire({
            title: '¡Trabajo finalizado!',
            text: 'El tiempo ha sido registrado correctamente.',
            icon: 'success',
            confirmButtonColor: '#004a87'
        });
    } else {
        Swal.fire({
            title: 'Error',
            text: data.error || 'No se pudo finalizar.',
            icon: 'error',
            confirmButtonColor: '#004a87'
        });
    }
}


// ── Formulario de respuesta ───────────────────────────────────────────────────

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
    const container = document.getElementById('replyFormContainer');
    if (container) {
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}


// ── Arranque ──────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {

    // Editor de respuesta
    if (document.getElementById('replyMessage')) {
        initReplyEditor('#replyMessage');
    }

    // Botones abrir/cerrar formulario de respuesta
    const replyContainer = document.getElementById('replyFormContainer');
    const btnOpen        = document.getElementById('btnOpenReply');
    const btnClose       = document.getElementById('btnCloseReply');

    const toggleForm = () => {
        const isHidden = replyContainer.style.display === 'none';
        replyContainer.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            replyContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    if (btnOpen)  btnOpen.addEventListener('click', toggleForm);
    if (btnClose) btnClose.addEventListener('click', () => replyContainer.style.display = 'none');

    // Añadir más archivos adjuntos
    const btnAddFile = document.getElementById('addFileReply');
    if (btnAddFile) {
        btnAddFile.addEventListener('click', (e) => {
            e.preventDefault();
            const container = document.getElementById('inputsAdjuntos');
            const wrapper   = document.createElement('div');
            wrapper.style.marginTop = '10px';
            const input = document.createElement('input');
            input.type      = 'file';
            input.name      = 'fileUsers[]';
            input.className = 'file-input';
            wrapper.appendChild(input);
            container.appendChild(wrapper);
        });
    }

    // Validación y loading al enviar respuesta
    const formReply = document.getElementById('formReplyTicket');
    if (formReply) {
        formReply.addEventListener('submit', function(e) {
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'zip', 'sql'];
            const files   = formReply.querySelectorAll('input[name="fileUsers[]"]');
            let isValid   = true;

            files.forEach(input => {
                if (input.files.length > 0) {
                    const ext = input.files[0].name.split('.').pop().toLowerCase();
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

    // Sistema de trabajo (solo si el bloque existe en el DOM, es decir, es staff)
    if (document.getElementById('work-session-box')) {
        loadStatus();
    }

});