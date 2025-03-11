import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')
    const point_user_month = $('#point-user-month')
    const point_user_year = $('#point-user-year')
    const karyawan_filter = $('#karyawan-filter')
    const group_filter = $('.group-filter')

    fetchData()

    point_user_month.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    point_user_year.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    group_filter.on('change', function(e) {
        e.preventDefault();
        fetchData();
    });
    
    function fetchData() {
        let query = new URLSearchParams({
            month: point_user_month.val(),
            year: point_user_year.val(),
            user_uuid: karyawan_filter.val(),
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/point_user/grid?' + query,
            column: [
                { title: "Bulan", data: 'bulan' },
                { title: "Tahun", data: 'tahun' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Jumlah Point", data: 'jumlah_point' },
            ]
        }, false, false)
    }

    datatableHandleDelete({
        html: datatable,
        url: '/point_user/'
    })

})
