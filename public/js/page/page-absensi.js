import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js";

$(function () {
    'use strict';

    const datatable = $('#datatable');
    const karyawan_filter = $('#karyawan-filter');
    const kategori_absensi_filter = $('#kategori-absensi-filter');
    const absensi_date_filter = $('#absensi-date-filter');

    // Debounce helper
    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Fetch Data function
    function fetchData() {
        let user_uuid = karyawan_filter.val();
        let kategori_uuid = kategori_absensi_filter.val();
        let date_range = absensi_date_filter.val();

        // Set default date to today if date_range kosong
        if (!date_range) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            date_range = `${yyyy}-${mm}-${dd} to ${yyyy}-${mm}-${dd}`;
            absensi_date_filter.val(date_range); // update di input juga
        }

        console.log('karyawan_filter:', user_uuid);
        console.log('kategori_filter:', kategori_uuid);
        console.log('date_range:', date_range);

        const query = new URLSearchParams({
            user_uuid: user_uuid,
            kategori_absensi_uuid: kategori_uuid,
            date_range: date_range
        }).toString();

        console.log("QUERY URL:", '/absensi/grid?' + query);

        datatableHandleFetchData({
            html: datatable,
            url: '/absensi/grid?' + query,
            column: [
                { title: "Tanggal", data: 'tanggal' },
                { title: "Nama Karyawan", data: 'nama_karyawan' },
                { title: "Kategori", data: 'nama_kategori' },
                { title: "Status Kehadiran", data: 'status_absen' },
                { title: "Keterangan", data: 'keterangan' },
                { title: "Jumlah Point", data: 'jumlah_point' },
                {
                    title: "Bukti Foto",
                    data: 'bukti_foto_url',
                    orderable: false,
                    searchable: false,
                    render: function (url) {
                        return url
                            ? `<img src="${url}" style="max-width:100px; max-height:100px; border-radius:4px;" alt="Bukti Foto">`
                            : '-';
                    }
                }
            ]
        });
    }

    const debouncedFetchData = debounce(fetchData, 300);

    // flatpickr init
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#absensi-date-filter", {
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function () {
                debouncedFetchData();
            }
        });
    }

    // Change event
    karyawan_filter.on('change', debouncedFetchData);
    kategori_absensi_filter.on('change', debouncedFetchData);

    // Load default data
    fetchData();

    // Delete handler
    datatableHandleDelete({
        html: datatable,
        url: '/absensi/'
    });
});
