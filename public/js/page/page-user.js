import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js";

$(function () {
    const datatable = $('#datatable');
    const role_filter = $('#role-filter');
    const currentUserRoleId = parseInt(datatable.data('role-id'), 10);

    const columns = [
        { width: "15%", title: "Foto Profil", data: 'profile_picture' },
        { width: "15%", title: "Tanda Tangan", data: 'signature_file' },
        { title: "Nama", data: 'user_information.nama' },
        { title: "Email", data: 'email' },
        { title: "Nomor Telepon", data: 'user_information.notlp' },
        { title: "Alamat", data: 'user_information.alamat' },
        { title: "Role", data: 'user_role.role.name' },
    ];

    if (currentUserRoleId === 1) {
        columns.push({
            title: "Password",
            data: "password",
            orderable: false
        });
    }

    function fetchData() {
        const role_uuid = role_filter.val();
        console.log("Selected role_uuid:", role_uuid);
    
        let query = new URLSearchParams({
            role_uuid: role_uuid ?? ''
        }).toString();
    
        // ✅ Cek jika datatable sudah ada, destroy dulu
        if ($.fn.DataTable.isDataTable(datatable)) {
            datatable.DataTable().clear().destroy();
            datatable.empty(); // Kosongkan tabel agar tidak konflik kolom
        }
    
        datatableHandleFetchData({
            html: datatable,
            url: '/user/grid?' + query,
            column: columns
        });
    }
    

    role_filter.on('change', function () {
        fetchData();
    });

    fetchData();

    datatableHandleDelete({
        html: datatable,
        url: '/user/'
    });
});
