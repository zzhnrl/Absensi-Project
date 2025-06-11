import { datatableHandleFetchData, datatableHandleDelete } from "/js/helper/datatable.js";

$(function() {
  const dt = $('#datatable');
  const filters = {
    date: $('#cuti-date-filter'),
    user: $('#karyawan-filter'),
    status: $('#status-filter'),
  };

  function fetchData() {
    const query = new URLSearchParams({
      user_uuid: filters.user.val(),
      status_cuti_uuid: filters.status.val(),
      date_range: filters.date.val(),
    }).toString();

    console.log("Requesting with filter:", {
      user_uuid: filters.user.val(),
      status_cuti_uuid: filters.status.val(),
      date_range: filters.date.val(),
    });

    datatableHandleFetchData({
      html: dt,
      url: '/cuti/grid?' + query,
      column: [
        { data: 'tanggal_mulai', title: 'Tanggal Mulai' },
        { data: 'tanggal_akhir', title: 'Tanggal Akhir' },
        { data: 'sisa_cuti', title: 'Kuota Cuti' },
        { data: 'nama_karyawan', title: 'Nama Karyawan' },
        { data: 'keterangan', title: 'Keterangan' },
        { data: 'jenis_cuti', title: 'Jenis Cuti' },
        { data: 'jumlah_cuti', title: 'Jumlah Cuti' },
        {
          data: 'status_cuti.nama',
          title: 'Status',
          render: (data) => {
            const cls = {
              "Diajukan": 'text-warning',
              "Disetujui": 'text-success',
              "Ditolak": 'text-danger'
            }[data] || '';
            return `<span class="${cls} font-weight-bold">${data}</span>`;
          }
        },
        { data: 'approval', title: 'Approval' },
        { data: 'tanggal_keputusan', title: 'Tgl Keputusan' },
        { data: 'pemberi_keputusan', title: 'Pemberi Keputusan' },
        { data: 'action', title: 'Action', orderable: false, searchable: false }
      ]
    });
  }

  // Bind filter events
  filters.date.on('change', fetchData);
  filters.user.on('change', fetchData);
  filters.status.on('change', fetchData);

  // Initialize
  fetchData();

  datatableHandleDelete({
    html: dt,
    url: '/cuti/'
  });

  // Optional: you already used event delegation for approving/denying cuti
});
