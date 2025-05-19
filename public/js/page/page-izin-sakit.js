import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')
    const group_filter = $('.group-filter')
    const izin_sakit_date_filter = $('#izin-sakit-date-filter')
    const karyawan_filter = $('#karyawan-filter')

    fetchData()

    group_filter.on('change', function(e) {
        e.preventDefault();
        fetchData();
    });

    izin_sakit_date_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    function fetchData() {
        let query = new URLSearchParams({
            date_range : izin_sakit_date_filter.val(),
            user_uuid : karyawan_filter.val()
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/izin_sakit/grid?' + query,
            column: [
                { title: "Tanggal", data: 'tanggal' },
                { width: "15%", title: "Foto Bukti", data: 'pbukti' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Keterangan", data: 'keterangan' },
            ],
            error(xhr, status, err) {
                console.error('Izin Sakit Ajax Error:', status, err);
                console.error('Response Text:', xhr.responseText);
            }
        })
    }

    datatableHandleDelete({
        html: datatable,
        url: '/izin_sakit/'
    })

})
