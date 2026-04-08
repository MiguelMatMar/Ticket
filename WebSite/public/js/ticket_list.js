let ticketTable;

$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#tableTicketsList')) {
        $('#tableTicketsList').DataTable().destroy();
    }

    ticketTable = $('#tableTicketsList').DataTable({
        "dom": 'rt',
        "pageLength": 10,
        "order": [[3, "desc"]],
        "columnDefs": [
            { "targets": [1, 2], "type": "string" }
        ],
        "language": {
            "zeroRecords": "No se encontraron resultados"
        },
        "drawCallback": function() {
            const api = this.api();
            const info = api.page.info();
            
            let start = info.recordsTotal > 0 ? info.start + 1 : 0;
            $('#ticket-table-info').text(
                `Viendo ${start} a ${info.end} de ${info.recordsDisplay} entradas`
            );
            
            $('#prevTicket').prop('disabled', info.page === 0);
            $('#nextTicket').prop('disabled', info.page >= info.pages - 1 || info.pages === 0);

            let counts = {
                'open': 0,
                'answered': 0,
                'closed': 0
            };

            api.rows().every(function() {
                const status = $(this.node()).find('td').eq(2).data('filter');
                if (counts.hasOwnProperty(status)) {
                    counts[status]++;
                }
            });

            $('.lookInfo[onclick*="open"] p').text(counts['open']);
            $('.lookInfo[onclick*="answered"] p').text(counts['answered']);
            $('.lookInfo[onclick*="closed"] p').text(counts['closed']);
        }
    });

    $('#ticket-search').on('keyup', function() {
        ticketTable.search(this.value).draw();
    });

    $('#filter-dept').on('change', function() {
        const val = $(this).val();
        ticketTable.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
    });

    $('#ticket-length').on('change', function() {
        ticketTable.page.len(parseInt($(this).val())).draw();
    });

    $('#prevTicket').on('click', function(e) { 
        e.preventDefault();
        ticketTable.page('previous').draw('page'); 
    });

    $('#nextTicket').on('click', function(e) { 
        e.preventDefault();
        ticketTable.page('next').draw('page'); 
    });
});

function filterByStatus(status) {
    if (ticketTable) {
        if (status === '') {
            ticketTable.column(2).search('').draw();
        } else {
            ticketTable.column(2).search('^' + status + '$', true, false).draw();
        }
    }
}