$(function () {
    'use strict';

    const datatable = $('#datatable');
    const point_user_month = $('#point-user-month');
    const point_user_year = $('#point-user-year');
    const karyawan_filter = $('#karyawan-filter');
    const group_filter = $('.group-filter');

    let tableInstance;

    function initDatatable() {
        if (tableInstance) {
            tableInstance.destroy();
        }
        tableInstance = datatable.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/point_user/grid',
                data: function (d) {
                    d.month = point_user_month.val();
                    d.year = point_user_year.val();
                    d.user_uuid = karyawan_filter.val();
                }
            },
            columns: [
                { title: "Bulan", data: 'bulan' },
                { title: "Tahun", data: 'tahun' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Jumlah Point", data: 'jumlah_point' },
                { title: "Point Per Tahun", data: 'jumlah_point_per_tahun' }
            ],
            order: [[1, 'desc']], // contoh sorting by tahun desc
            lengthMenu: [10, 25, 50],
            pageLength: 10,
        });
    }

    // Initialize datatable first time
    initDatatable();

    // Re-fetch data on filter change
    point_user_month.on('change', function () {
        tableInstance.ajax.reload();
    });

    point_user_year.on('change', function () {
        tableInstance.ajax.reload();
    });

    karyawan_filter.on('change', function () {
        tableInstance.ajax.reload();
    });

    group_filter.on('change', function () {
        tableInstance.ajax.reload();
    });

    // Optional: handle delete if needed with datatableHandleDelete or custom code
});
