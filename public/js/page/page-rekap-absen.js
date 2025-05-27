import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')
    const karyawan_filter = $('#karyawan-filter')
    const group_filter = $('.group-filter')
    const rekap_izin_sakit_month = $('#rekap-izin-sakit-month')
    const rekap_izin_sakit_year = $('#rekap-izin-sakit-year')

    fetchData()

    group_filter.on('change', function(e) {
        e.preventDefault();
        fetchData();
    });

    rekap_izin_sakit_month.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    rekap_izin_sakit_year.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    karyawan_filter.on('change', function(e) {
        e.preventDefault();
        fetchData();
    });
    
    function fetchData() {
        let query = new URLSearchParams({
            month: rekap_izin_sakit_month.val(),
            year: rekap_izin_sakit_year.val(),
            user_uuid: karyawan_filter.val(),
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/rekap_izin_sakit/grid?' + query,
            columns: [
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "WFO", data: 'WFO'},
                { title: "WFH", data: 'WFH'},
                { title: "Jumlah Poin", data: 'jumlah_point' },
            ],
        }, false, false)
    }

    datatableHandleDelete({
        html: datatable,
        url: '/jumlah_izin_sakit/'
    })

})
