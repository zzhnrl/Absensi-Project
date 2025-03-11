import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')

    datatableHandleFetchData({
        html: datatable,
        url: '/role/grid',
        column: [
            { title: "Nama", data: 'name' },
            { title: "Kode", data: 'code' },
            { title: "Deskripsi", data: 'description' },
        ]
    })

    datatableHandleDelete({
        html: datatable,
        url: '/role/'
    })
})
