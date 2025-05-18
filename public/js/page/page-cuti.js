import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js"
$(function () {
    'use strict';
    //init variable
    var cuti_uuid;

    const datatable = $('#datatable')
    const karyawan_filter = $('#karyawan-filter')
    const kategori_absensi_filter = $('#kategori-absensi-filter')
    const cuti_date_filter = $('#cuti-date-filter')
    const status_filter = $('#status-filter')

    fetchData()

    karyawan_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    cuti_date_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    status_filter.on('change', function (e) {
        e.preventDefault()
        fetchData()
    })

    function fetchData() {
        let query = new URLSearchParams({
            user_uuid : karyawan_filter.val(),
            status_cuti_uuid : status_filter.val(),
            date_range : cuti_date_filter.val()
        }).toString()
        datatableHandleFetchData({
            html: datatable,
            url: '/cuti/grid?' + query,
            column: [
                { title: "Tanggal Mulai Cuti", data: 'tanggal_mulai' },
                { title: "Tanggal Akhir Cuti", data: 'tanggal_akhir' },
                { title: "Kuota Cuti", data: 'sisa_cuti' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Keterangan", data: 'keterangan' },
                { title: "Status", data: 'status_cuti.nama',
                    render: function(data, type, row) {
                        let statusClass = '';
                        if (data === 'Diajukan') {
                            statusClass = 'text-warning';
                        } else if (data === 'Disetujui') {
                            statusClass = 'text-success';
                        } else if (data === 'Ditolak') {
                            statusClass = 'text-danger';
                        }
                        return `<span class="${statusClass} font-weight-bold">${data}</span>`
                    }
                },
                { title: "Approval", data: 'approval' },
                { title: "Tanggal Keputusan", data: 'tanggal_keputusan' },
                { title: "Pemberi Keputusan", data: 'pemberi_keputusan' },
            ]
        })
    }

    datatableHandleDelete({
        html: datatable,
        url: '/cuti/'
    })

    datatable.on('click','tbody .setujui', function (e) {
        cuti_uuid = this.value;

        e.preventDefault();
        
        Swal.fire({
            text: 'Apakah kamu yakin akan menyetujui cuti ini?',
            icon: 'warning',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: 'Ya, setujui!',
            cancelButtonText: 'Tidak, kembali!',
            customClass: {
                confirmButton: 'btn btn-success mr-2',
                cancelButton: 'btn btn-secondary',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '/cuti/' + cuti_uuid + '/setujui',
                    async: false,
                    type: 'put',
                    success: function(data){
                        Swal.fire('Success', 'Cuti berhasil disetujui', 'success');
                        
                        setTimeout(function() {
                            window.location.href = "/cuti";
                        }, 5000);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            text: 'Gagal menyetujui cuti.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-danger',
                            }
                        });
                    }
                })
            }
        });
    });

    datatable.on('click','tbody .tolak', function (e) {
        cuti_uuid = this.value;

        e.preventDefault();
        
        Swal.fire({
            text: 'Apakah kamu yakin akan menolak cuti ini?',
            icon: 'warning',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: 'Ya, tolak!',
            cancelButtonText: 'Tidak, kembali!',
            customClass: {
                confirmButton: 'btn btn-success mr-2',
                cancelButton: 'btn btn-secondary',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : '/cuti/' + cuti_uuid + '/tolak',
                    async: false,
                    type: 'put',
                    success: function(data){
                        Swal.fire('Success', 'Cuti berhasil ditolak', 'success');
                        
                        setTimeout(function() {
                            window.location.href = "/cuti";
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            text: 'Gagal menolak cuti.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-danger',
                            }
                        });
                    }
                })
            }
        });
    });
})