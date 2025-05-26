import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')
    const karyawan_filter = $('#karyawan-filter')
    const kategori_absensi_filter = $('#kategori-absensi-filter')
    const absensi_date_filter = $('#absensi-date-filter')

    fetchData()

    karyawan_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    kategori_absensi_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    absensi_date_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    function fetchData() {
        let query = new URLSearchParams({
            user_uuid : karyawan_filter.val(),
            kategori_absensi_uuid : kategori_absensi_filter.val(),
            date_range : absensi_date_filter.val()
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/absensi/grid?' + query,
            column: [
                { title: "Tanggal", data: 'tanggal' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Kategori", data: 'nama_kategori' },
                { title: "Status Kehadiran", data: 'status_absen'},
                { title: "Keterangan", data: 'keterangan' },
                { title: "Jumlah Point", data: 'jumlah_point' },

                {
                    title:      "Bukti Foto",
                    data:       'bukti_foto_url',    // properti yang dikirim service
                    orderable:  false,
                    searchable: false,
                    render:     function(url) {
                      return url
                        ? `<img src="${url}" 
                                style="max-width:100px; max-height:100px; border-radius:4px;"
                                alt="Bukti Foto">`
                        : '-';
                    }
                  }
                
            ]
        })
    }

    datatableHandleDelete({
        html: datatable,
        url: '/absensi/'
    })

})
