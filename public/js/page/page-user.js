import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    const datatable = $('#datatable')
    const role_filter = $('#role-filter')

    fetchData()

    role_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    function fetchData() {
        let query = new URLSearchParams({
            role_uuid : role_filter.val()
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/user/grid?' + query,
            column: [
                { width: "15%", title: "Foto Profil", data: 'profile_picture' },
                { width: "15%", title: "Tanda Tangan", data: 'signature_file' },
                { title: "Nama", data: 'user_information.nama' },
                { title: "Email", data: 'email' },
                { title: "Nomor Telepon", data: 'user_information.notlp' },
                { title: "Alamat", data: 'user_information.alamat' },
                { title: "Role", data: 'user_role.role.name' },
            ]
        })
    }

    datatableHandleDelete({
        html: datatable,
        url: '/user/'
    })

})
