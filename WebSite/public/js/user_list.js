let userTable;
let colRol;

$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#tableTicketsList')) {
        $('#tableTicketsList').DataTable().destroy();
    }

    const headers = [];
    $('#tableTicketsList thead th').each(function() {
        headers.push($(this).text().trim());
    });

    colRol               = headers.indexOf('Rol');
    const colDate        = headers.indexOf('Registro');
    const colActions     = headers.indexOf('Acciones');

    userTable = $('#tableTicketsList').DataTable({
        "dom": 'rt',
        "pageLength": 10,
        "order": [[colDate, "desc"]],
        "language": {
            "zeroRecords": "No se encontraron resultados"
        },
        "columnDefs": [
            { "orderable": false, "targets": [colRol, colActions] }
        ],
        "drawCallback": function() {
            const api = this.api();
            const info = api.page.info();

            let start = info.recordsTotal > 0 ? info.start + 1 : 0;
            $('#ticket-table-info').text(
                `Viendo ${start} a ${info.end} de ${info.recordsDisplay} entradas`
            );

            $('#prevTicket').prop('disabled', info.page === 0);
            $('#nextTicket').prop('disabled', info.page >= info.pages - 1 || info.pages === 0);

            // Contar TODOS los registros ignorando filtros activos
            // Lee el span.rol-value oculto en lugar del select
            let counts = { 'cliente': 0, 'soporte': 0, 'admin': 0 };

            api.rows().every(function() {
                const rol = $(this.node()).find('.rol-value').text().trim();
                if (counts.hasOwnProperty(rol)) {
                    counts[rol]++;
                }
            });

            $('.lookInfo[onclick*="\'cliente\'"] p').text(counts['cliente']);
            $('.lookInfo[onclick*="\'soporte\'"] p').text(counts['soporte']);
            $('.lookInfo[onclick*="\'admin\'"] p').text(counts['admin']);
        }
    });

    $('#ticket-search').on('keyup', function() {
        userTable.search(this.value).draw();
    });

    $('#filter-rol').on('change', function() {
        applyRolFilter($(this).val());
    });

    $('#ticket-length').on('change', function() {
        userTable.page.len(parseInt($(this).val())).draw();
    });

    $('#prevTicket').on('click', function(e) {
        e.preventDefault();
        userTable.page('previous').draw('page');
    });

    $('#nextTicket').on('click', function(e) {
        e.preventDefault();
        userTable.page('next').draw('page');
    });
});

// Filtro personalizado: lee el span.rol-value oculto de cada fila
let activeRolFilter = '';

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    if (settings.nTable.id !== 'tableTicketsList') return true;
    if (activeRolFilter === '') return true;
    const row = userTable ? userTable.row(dataIndex).node() : null;
    if (!row) return true;
    const rol = $(row).find('.rol-value').text().trim();
    return rol === activeRolFilter;
});

function applyRolFilter(val) {
    activeRolFilter = val;
    if (userTable) userTable.draw();
}

function filterByRol(rol) {
    applyRolFilter(rol);
    $('#filter-rol').val(rol);
}

function swConfirm(e, el, title, text, icon, confirmText) {
    e.preventDefault();
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: icon === 'error' ? '#b71c1c' : '#18507F',
        cancelButtonColor: '#888',
        reverseButtons: true
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = el.href;
        }
    });
    return false;
}

// Detectar cambio en el select de rol
document.querySelectorAll('.update-role-select').forEach(select => {
    select.addEventListener('change', function() {
        const userId = this.getAttribute('data-user-id');
        const newRole = this.value;
        const userName = this.closest('tr').querySelector('td:nth-child(2)').innerText;

        Swal.fire({
            title: '¿Cambiar rol?',
            text: `¿Deseas cambiar el rol de ${userName} a ${newRole}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#18507F',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `/support/update_user_role?userId=${userId}&role=${newRole}`;
            } else {
                location.reload();
            }
        });
    });
});