import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')

    datatableHandleFetchData({
        html: datatable,
        url: '/kategori_absensi/grid',
        column: [
            { title: "Kategori", data: 'name' },
            { title: "Kode", data: 'code' },
            { title: "Point", data: 'point' },
            { title: "Deskripsi", data: 'description' },
        ],
    })

    datatableHandleDelete({
        html: datatable,
        url: '/kategori_absensi/'
    })
})
