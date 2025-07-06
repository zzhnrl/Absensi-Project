
export function datatableHandleFetchData ({html, url, column,columnDefs=null, searching = true}, withAction = true, withCreated = true) {
    if (withCreated && !column?.find(col => col.data === 'created_at')) column.push({
        title: "Created At",
        data: 'created_at',
        defaultContent: '-',
        render: function (data) {
            if (!data) return '-';

            // Coba sebagai Unix timestamp
            if (!isNaN(data) && String(data).length <= 11) {
                return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
            }

            // Coba sebagai string waktu standar
            const parsed = moment(data, 'YYYY-MM-DD HH:mm:ss', true);
            return parsed.isValid() ? parsed.format("DD/MM/YYYY HH:mm:ss") : '-';
        }
    });

    if (withAction && !column?.find(col => col.data === 'action')) column.push({ width: '10%',title: "Action", data: 'action' })

    const table = html.DataTable({
        destroy: true,
        responsive: true,
        serverSide: true,
        async: true,
        ordering: false,
        searching: searching,
        processing: true,
        paginate: true,
        "language": {
            processing: '<div style="margin-top:-25px"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>'
        },
        "ajax": url,
        columns: column,
        columnDefs: columnDefs
    });
    return table; // <- ini penting
}

export function datatableHandleEvent ({html, onEvent, classEvent, triggerEvent}) {
    html.on( onEvent , classEvent, triggerEvent);
}

export function refreshDataTable(html){
    html.DataTable().ajax.reload(null, false);
}

export function datatableHandleDelete ({html,url}) {
    html.on( 'click', 'tbody .delete', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tolong hapus!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url + this.value, {
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'DELETE'
                }).then((res)  => {
                    if (res.status == 200) {
                    Swal.fire('Success', 'Data berhasil dihapus','success')
                    html.DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Oops', 'Gagal menghapus data','error')
                    }

                })
                .catch(err => console.log(err))
            }
        })
    });

    
}

