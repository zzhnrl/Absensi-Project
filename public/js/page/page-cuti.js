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

  // Handle setujui button click
  dt.on('click', '.setujui', function(e) {
    e.preventDefault();
    const cutiUuid = $(this).val();
    
    Swal.fire({
      title: 'Setujui Cuti?',
      text: "Apakah Anda yakin ingin menyetujui cuti ini?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Setujui!',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        fetch(`/cuti/${cutiUuid}/setujui`, {
          headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          method: 'PUT'
        }).then((res) => res.json())
        .then((data) => {
          if (data.success) {
            Swal.fire('Berhasil!', data.message, 'success');
            dt.DataTable().ajax.reload(null, false);
          } else {
            Swal.fire('Gagal!', data.message, 'error');
            // Reset button state
            $(this).prop('disabled', false).html('<i class="fas fa-check"></i> Setujui');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan', 'error');
          // Reset button state
          $(this).prop('disabled', false).html('<i class="fas fa-check"></i> Setujui');
        });
      }
    });
  });

  // Handle tolak button click
  dt.on('click', '.tolak', function(e) {
    e.preventDefault();
    const cutiUuid = $(this).val();
    
    Swal.fire({
      title: 'Tolak Cuti?',
      text: "Apakah Anda yakin ingin menolak cuti ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Tolak!',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        fetch(`/cuti/${cutiUuid}/tolak`, {
          headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          method: 'PUT'
        }).then((res) => res.json())
        .then((data) => {
          if (data.success) {
            Swal.fire('Berhasil!', data.message, 'success');
            dt.DataTable().ajax.reload(null, false);
          } else {
            Swal.fire('Gagal!', data.message, 'error');
            // Reset button state
            $(this).prop('disabled', false).html('<i class="fas fa-times"></i> Tolak');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan', 'error');
          // Reset button state
          $(this).prop('disabled', false).html('<i class="fas fa-times"></i> Tolak');
        });
      }
    });
  });
});
