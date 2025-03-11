import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')

    datatableHandleFetchData({
        html: datatable,
        url: '/status_cuti/grid',
        column: [
            { title: "Status", data: 'nama' },
            { title: "Kode", data: 'kode' },
            { title: "Deskripsi", data: 'deskripsi' },
        ]
    })

    datatableHandleDelete({
        html: datatable,
        url: '/status_cuti/'
    })
})
