<div class="row">
  <div class="col-12">
    {{-- Tanggal --}}
    <div class="form-group">
      <label>Tanggal</label>
      <input 
        type="text" 
        class="flatpickr-daterange form-control" 
        id="absensi-date" 
        value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}"
        disabled
      >
    </div>

    {{-- Waktu Absensi --}}
    <div class="form-group">
      <label>Waktu Absensi</label>
      <input 
        type="text" 
        class="form-control" 
        id="current-time" 
        name="waktu_absensi" 
        readonly
      >
    </div>

    {{-- Nama Karyawan --}}
    <div class="form-group">
      <label>Nama Karyawan</label>
      <select 
        class="select2 form-control @error('user_uuid') is-invalid @enderror"
        name="user_uuid" 
        id="user_uuid" 
        style="width:100%"
      >
        <option value="">-- Pilih Nama Karyawan --</option>
        @foreach ($users as $user)
          <option value="{{ $user->uuid }}">{{ $user->userInformation->nama }}</option>
        @endforeach
      </select>
      @error('user_uuid')
        <span class="text-danger">{{ $message }}</span>
      @enderror
    </div>

    {{-- Status Absensi --}}
    <div class="form-group">
      <label>Status Absensi</label>
      <input 
        type="text" 
        class="form-control" 
        id="status-absensi" 
        name="status_absensi" 
        readonly
      >
    </div>

    {{-- Kategori (jika perlu) --}}
    @if (!isset($absensi) or ($absensi->kategori_absensi->id ?? null) != 1)
    <div class="form-group">
      <label>Kategori</label>
      <select 
        class="select2 form-control @error('kategori_absensi_uuid') is-invalid @enderror" 
        name="kategori_absensi_uuid"
      >
        {!! each_option(
             $kategori_absensis, 
             'name', 
             old('kategori_absensi_uuid', $absensi->kategori_absensi->uuid ?? '')
           ) !!}
      </select>
      @error('kategori_absensi_uuid')
        <span class="text-danger">{{ $message }}</span>
      @enderror
    </div>
    @endif

    {{-- Keterangan --}}
    <div class="form-group">
      <label>Keterangan</label>
      <textarea 
        class="form-control @error('keterangan') is-invalid @enderror" 
        name="keterangan" 
        placeholder="Masukkan keterangan tambahan"
      >{{ old('keterangan') }}</textarea>
      @error('keterangan')
        <span class="text-danger">{{ $message }}</span>
      @enderror
    </div>
    {{-- Bukti Foto di Kantor --}}
    <div class="form-group">
      <label>Bukti Foto di Kantor</label>
      <input 
        type="file" 
        name="bukti_foto_dikantor" 
        accept="image/*" 
        class="form-control @error('bukti_foto_dikantor') is-invalid @enderror"
        required
      >
      <small class="form-text text-muted">*Maks 10 Mb dan File wajib PNG atau JPG</small> <!-- ini tambahan teksnya -->
      @error('bukti_foto_dikantor')
        <span class="text-danger">{{ $message }}</span>
      @enderror
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // inisialisasi flatpickr untuk tanggal (meski disable)
    flatpickr("#absensi-date", {
        dateFormat: "Y-m-d",
        defaultDate: "today"
    });

    // update waktu & status setiap detik
    updateTime();
    setInterval(updateTime, 1000);
});

function updateTime() {
    const now = new Date();
    const h   = now.getHours();
    const m   = now.getMinutes();
    const s   = now.getSeconds();
    const timeStr = 
      `${String(h).padStart(2,'0')}:` +
      `${String(m).padStart(2,'0')}:` +
      `${String(s).padStart(2,'0')}`;

    // tampilkan di input Waktu
    document.getElementById("current-time").value = timeStr;

    // hitung status berdasarkan menit sejak 00:00
    const totalMinutes = h * 60 + m;
    const hadirStart   = 9 * 60;       // 09:00 → 540
    const hadirEnd     = 10 * 60;      // 10:00 → 600
    const terlambatStart = hadirEnd + 1; // 10:01 → 601
    const terlambatEnd   = 17 * 60;     // 17:00 → 1020

    let status;
    if (totalMinutes >= hadirStart && totalMinutes <= hadirEnd) {
        status = "Hadir";
    } else if (totalMinutes >= terlambatStart && totalMinutes <= terlambatEnd) {
        status = "Terlambat";
    } else {
        status = "Alpha / Tidak Hadir";
    }

    document.getElementById("status-absensi").value = status;
}
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_SIZE_MB = 10;
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

    const buktiFotoInput = document.querySelector('input[name="bukti_foto_dikantor"]');

    if (buktiFotoInput) {
        buktiFotoInput.addEventListener('change', function (event) {
            const file = event.target.files[0];

            if (file && file.size > MAX_SIZE_BYTES) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran File Terlalu Besar',
                    text: 'Maksimum ukuran file adalah 10MB.',
                    confirmButtonColor: '#d33'
                });

                // Reset input agar file tidak dikirim
                event.target.value = "";
            }
        });
    }
});
</script>

