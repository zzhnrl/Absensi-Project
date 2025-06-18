$(document).ready(function() {
    const datatable = $('#datatable');
    const filterSelectors = '#rekap-izin-sakit-month, #rekap-izin-sakit-year, #karyawan-filter';

    function fetchData() {
        // Ambil nilai filter
        let month = $('#rekap-izin-sakit-month').val();
        let year = $('#rekap-izin-sakit-year').val();
        let user_uuid = $('#karyawan-filter').val();

        let query = new URLSearchParams({ month, year, user_uuid }).toString();

        // Jika DataTable sudah aktif, destroy dulu supaya tidak dobel
        if ($.fn.DataTable.isDataTable(datatable)) {
            datatable.DataTable().clear().destroy();
        }

        // Inisialisasi DataTable baru
        datatable.DataTable({
            processing: true,
            serverSide: true,
            ordering: false,

            ajax: {
                url: '/rekap_izin_sakit/grid?' + query,
                type: 'GET',
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    console.error(xhr.responseText);
                }
            },
            columns: [
                { data: 'bulan', title: 'Bulan', orderable: false },
                { data: 'tahun', title: 'Tahun', orderable: false },
                { data: 'nama_karyawan', title: 'Nama Karyawan', orderable: false },
                { data: 'jumlah_izin_sakit', title: 'Jumlah Izin Sakit', orderable: false }
            ],
            lengthChange: false,
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                emptyTable: "Data tidak ditemukan"
            }
        });
    }

    // Pasang event change untuk semua filter sekaligus
    $(filterSelectors).on('change', function() {
        fetchData();
    });

    // Load data pertama kali
    fetchData();
});
