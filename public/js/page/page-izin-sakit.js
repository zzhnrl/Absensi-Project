// file: izin_sakit.js
import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js";

$(document).ready(function () {
    const datatable = $('#datatable');

    function fetchData() {
        let month = $('#rekap-izin-sakit-month').val();
        let year = $('#rekap-izin-sakit-year').val();
        let user_uuid = $('#karyawan-filter').val();

        let query = new URLSearchParams({ month, year, user_uuid }).toString();
        let ajaxUrl = '/izin_sakit/grid?' + query;

        if ($.fn.DataTable.isDataTable(datatable)) {
            datatable.DataTable().clear().destroy();
        }

        datatable.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: ajaxUrl,
                type: 'GET',
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    console.error(xhr.responseText);
                }
            },
            columns: [
                { data: 'tanggal', title: 'Tanggal' },
                { 
                    data: 'pbukti', 
                    title: 'Foto Bukti',
                    render: function(data) {
                        if (!data) return '-';
                        return `<img src="${data}" alt="Bukti" style="max-width: 120px; max-height: 80px; object-fit: contain; border-radius: 4px; box-shadow: 0 0 5px rgba(0,0,0,0.1);" />`;
                    },
                    orderable: false,
                    searchable: false,
                    width: '15%'
                },
                { data: 'nama_karyawan', title: 'Nama Karyawan' },
                { data: 'keterangan', title: 'Keterangan' },
                { 
                    title: "Created At",
                    data: 'created_at',
                    defaultContent: '-',
                    render: function (data) {
                        if (!data) return '-';
            
                        if (!isNaN(data) && String(data).length <= 11) {
                            return moment.unix(data).format("DD/MM/YYYY HH:mm:ss");
                        }
            
                        const parsed = moment(data, 'YYYY-MM-DD HH:mm:ss', true);
                        return parsed.isValid() ? parsed.format("DD/MM/YYYY HH:mm:ss") : '-';
                    }
                },
                { 
                    data: 'action', 
                    title: 'Action', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-center',
                    width: '10%'
                }
            ],
            language: {
                emptyTable: "Data tidak ditemukan"
            },
            lengthChange: false,
            pageLength: 10,
            responsive: true,
            autoWidth: false
        });
    }

    // Load data pertama kali
    fetchData();

    // Event klik tombol filter
    $('#btn-filter').on('click', function(e) {
        e.preventDefault();
        fetchData();
    });

    // Auto fetch saat filter berubah
    $('.rekap-izin-sakit-filter').on('change', function() {
        fetchData();
    });
});
