import { datatableHandleFetchData } from "/js/helper/datatable.js";

let tableInstance;

$(function () {
    const datatable = $('#datatable');

    const getUrlWithParams = () => {
        const month = $('#month').val();
        const year = $('#year').val();
        const url = new URL(window.HISTORY_POINT_GRID_URL, window.location.origin);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        return url.toString();
    };

    // Inisialisasi datatable pertama kali
    tableInstance = datatable.DataTable({
        processing: true,
        serverSide: true,
        ordering: false,


        ajax: getUrlWithParams(),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false,  },
            { data: 'perubahan_point', name: 'perubahan_point', orderable: false },
            { data: 'jumlah_point', name: 'jumlah_point', orderable: false },
            { data: 'tanggal', name: 'tanggal', orderable: false }
        ]
    });

    // Saat form filter disubmit
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        tableInstance.ajax.url(getUrlWithParams()).load();  // Ganti URL dan reload data
    });
});
