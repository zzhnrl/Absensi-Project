import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"

$(function () {
    'use strict';

    const datatable         = $('#datatable');
    const role_filter       = $('#role-filter');
    // ambil role_id dari attribute
    const currentUserRoleId = parseInt(datatable.data('role-id'), 10);

    fetchData();

    role_filter.on('change', function (e) {
        e.preventDefault();
        fetchData();
    });

    function fetchData() {
        let query = new URLSearchParams({
            role_uuid: role_filter.val()
        }).toString();

        // definisi kolom dasar
        const columns = [
            { width: "15%", title: "Foto Profil", data: 'profile_picture' },
            { width: "15%", title: "Tanda Tangan", data: 'signature_file' },
            { title: "Nama", data: 'user_information.nama' },
            { title: "Email", data: 'email' },
            { title: "Nomor Telepon", data: 'user_information.notlp' },
            { title: "Alamat", data: 'user_information.alamat' },
            { title: "Role", data: 'user_role.role.name' },
        ];

        // kalau yang login role_id = 1, tambahkan kolom Password
        if (currentUserRoleId === 1) {
            columns.push({
              title: "Password",
              data: "password",   // sekarang sudah ada
              orderable: false
            });
          }
          

        datatableHandleFetchData({
            html: datatable,
            url: '/user/grid?' + query,
            column: columns
        });
    }

    datatableHandleDelete({
        html: datatable,
        url: '/user/'
    });
});
