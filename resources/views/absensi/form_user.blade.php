<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="form-control" name="date" 
                   id="absensi-date" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" disabled>
        </div>

        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" name="nama_karyawan" 
                   value="{{ auth()->user()->userInformation->nama }}" disabled>
        </div>

        <div class="form-group">
            <label>Waktu Absensi</label>
            <input type="text" class="form-control" id="current-time" name="waktu_absensi" readonly>
        </div>

        <div class="form-group">
            <label>Status Absensi</label>
            <input type="text" class="form-control" id="status-absensi" name="status_absensi" readonly>
        </div>

        <div class="form-group">
      <label>Jarak ke Kantor (meter)</label>
      <input type="text" class="form-control" id="distanceInfo" name="jarak_ke_kantor" readonly>
    </div>

    <div class="form-group">
      <label>Kategori (Otomatis)</label>
      <select id="kategori-absensi" class="select2 form-control" disabled>
        @foreach ($kategori_absensis as $kategori)
          <option value="{{ $kategori->uuid }}"
                  data-name="{{ $kategori->name }}">
            {{ $kategori->name }}
          </option>
        @endforeach
      </select>
      <input type="hidden" name="kategori_absensi_uuid" id="hidden-kategori">
    </div>


        <p hidden id="distanceInfo"></p>

        <div class="form-group mt-3">
            <label>Keterangan</label>
            <textarea class="form-control" placeholder="Masukkan keterangan tambahan" name="keterangan"></textarea>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("📌 Halaman Absensi Dimuat");
    updateTime();
    setInterval(updateTime, 1000);
    checkLocation();
});

function updateTime() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const currentTime = `${hours}:${minutes}:${seconds}`;
    document.getElementById("current-time").value = currentTime;

    console.log(`⏰ Waktu Sekarang: ${currentTime}`);

    // hitung status berdasarkan WIB
    let statusAbsensi;
    if (hours >= 9 && hours <= 10) {
        statusAbsensi = "Hadir";
    } else if (hours > 10 && hours < 17) {
        statusAbsensi = "Terlambat";
    } else {
        statusAbsensi = "Alpha / Tidak Hadir";
    }

    document.getElementById("status-absensi").value = statusAbsensi;
    console.log(`📌 Status Absensi: ${statusAbsensi}`);

    // Tunggu lokasi sebelum update kategori
}

function checkLocation() {
  if (!navigator.geolocation) {
    return alert("❌ Geolocation tidak didukung.");
  }

  navigator.geolocation.getCurrentPosition(pos => {
    const userLat = pos.coords.latitude,
          userLng = pos.coords.longitude,
          officeLat = parseFloat("{{ $office->latitude ?? 0 }}"),
          officeLng = parseFloat("{{ $office->longitude ?? 0 }}"),
          distance = getDistance(userLat, userLng, officeLat, officeLng);

    // Tampilkan jarak dalam meter
    document.getElementById("distanceInfo").value = distance.toFixed(2) + " m";

    // Pilih kategori: < 1 km → WFO, else WFH
    const uuid = pilihKategori(distance);
// jika kamu sudah include jQuery + Select2
const $sel = $('#kategori-absensi');
$sel.val(uuid)               // set value underlying <select>
    .trigger('change.select2'); // beri tahu Select2 untuk rerender
$('#hidden-kategori').val(uuid);

  }, err => {
    console.error(err);
    alert("Gagal mendapatkan lokasi: " + err.message);
  });
}

function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371000;
  const dLat = (lat2 - lat1) * Math.PI/180;
  const dLon = (lon2 - lon1) * Math.PI/180;
  const a = Math.sin(dLat/2)**2 +
            Math.cos(lat1 * Math.PI/180)*Math.cos(lat2 * Math.PI/180) *
            Math.sin(dLon/2)**2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function pilihKategori(jarak) {
  // jarak dalam meter: <1000m → WFO, >=1000m → WFH
  const nama = jarak < 1000 ? "WFO" : "WFH";
  console.log(`📌 Kategori Berdasarkan Jarak: ${nama}`);

  const dropdown = document.getElementById("kategori-absensi");
  for (let opt of dropdown.options) {
    if (opt.dataset.name === nama) {
      return opt.value;
    }
  }
  return "";
}

// Fungsi untuk update kategori absensi otomatis
function updateKategori(status) {
    let kategoriValue = "";

    if (status === "Hadir") {
        kategoriValue = "{{ optional($kategori_absensis->where('name', 'Hadir')->first())->uuid }}";
    } else if (status === "Terlambat") {0 


      
        kategoriValue = "{{ optional($kategori_absensis->where('name', 'Terlambat')->first())->uuid }}";
    } else {
        kategoriValue = "{{ optional($kategori_absensis->where('name', 'Alpha / Tidak Hadir')->first())->uuid }}";
    }

    console.log("📌 UUID Kategori Absensi yang Dipilih:", kategoriValue);

    if (kategoriValue) {
        document.getElementById("kategori-absensi").value = kategoriValue;
        document.getElementById("hidden-kategori").value = kategoriValue;
    } else {
        console.warn("⚠️ UUID Kategori Absensi Tidak Ditemukan!");
    }
}




</script>