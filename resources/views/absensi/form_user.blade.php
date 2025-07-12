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
    <label>Pilih Lokasi Kantor</label>
    <select class="form-control" id="kantor-terpilih" onchange="checkLocation()">

        <option value="">-- Pilih Lokasi Kantor --</option>
        @foreach($offices as $office)
            <option 
                value="{{ $office->latitude }},{{ $office->longitude }}"
                data-lat="{{ $office->latitude }}"
                data-lng="{{ $office->longitude }}">
                {{ $office->address }}
            </option>
        @endforeach
    </select>
</div>
<!-- <small id="accuracy-info" class="form-text text-muted"></small> --> <!-- untuk menampilkan akurasi gps -->



<div class="form-group">
  <label>Jarak ke Kantor (meter)</label>
  <input type="text" class="form-control" id="distanceInfo" name="jarak_ke_kantor" readonly>
  <!-- <small id="distance-text" class="form-text text-muted"></small> --> <!-- untuk menampilkan akurasi gps -->
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
  // Tambah log saat form disubmit
  const form = document.querySelector("form");
  if (form) {
    form.addEventListener("submit", function (e) {
      const selectedKategori = $('#kategori-absensi').val();
      $('#hidden-kategori').val(selectedKategori);
      console.log("📤 [FINAL SUBMIT] kategori_absensi_uuid:", selectedKategori);

      // validasi jika kosong
      if (!selectedKategori) {
        alert("Kategori absensi belum ditentukan.");
        e.preventDefault(); // batalkan submit
      }
    });
  }
});

function updateTime() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const currentTime = `${hours}:${minutes}:${seconds}`;
    document.getElementById("current-time").value = currentTime;

    //console.log(`⏰ Waktu Sekarang: ${currentTime}`);

    // hitung status berdasarkan WIB
    let statusAbsensi;
    if (hours >= 8 && hours <= 10) {
        statusAbsensi = "Hadir";
    } else if (hours > 10 && hours < 17) {
        statusAbsensi = "Terlambat";
    } else {
        statusAbsensi = "Alpha / Tidak Hadir";
    }

    document.getElementById("status-absensi").value = statusAbsensi;
    //console.log(`📌 Status Absensi: ${statusAbsensi}`);

    // Tunggu lokasi sebelum update kategori
}


let lokasiSudahDiperiksa = false;
let watchId;

document.addEventListener("DOMContentLoaded", function () {
  console.log("📌 Halaman Absensi Dimuat");
  updateTime();
  setInterval(updateTime, 1000);

  checkLocation();

  const form = document.querySelector("form");
  if (form) {
    form.addEventListener("submit", function (e) {
      if (!lokasiSudahDiperiksa) {
        alert("Tunggu hingga lokasi berhasil ditentukan terlebih dahulu.");
        e.preventDefault();
        return;
      }

      const selectedKategori = $('#kategori-absensi').val();
      $('#hidden-kategori').val(selectedKategori);
      console.log("📤 [FINAL SUBMIT] kategori_absensi_uuid:", selectedKategori);

      if (!selectedKategori) {
        alert("Kategori absensi belum ditentukan.");
        e.preventDefault();
      }
    });
  }
});

function checkLocation() {
  const selected = document.getElementById("kantor-terpilih");
  const value = selected.value;

  if (!value) {
    resetLocationInfo();
    return;
  }

  const [officeLat, officeLng] = value.split(',').map(parseFloat);

  if (!navigator.geolocation) {
    return alert("❌ Geolocation tidak didukung.");
  }

  if (watchId) {
    navigator.geolocation.clearWatch(watchId);
  }

  navigator.geolocation.getCurrentPosition(pos => {
    const userLat = pos.coords.latitude;
    const userLng = pos.coords.longitude;
    const accuracy = pos.coords.accuracy;

    console.log(`📍 Lokasi: ${userLat}, ${userLng}, Akurasi: ${accuracy} meter`);

    const distance = getDistance(userLat, userLng, officeLat, officeLng);
    document.getElementById("distanceInfo").value = distance.toFixed(2) + " m";

    const uuid = pilihKategori(distance);
    $('#kategori-absensi').val(uuid).trigger('change.select2');
    $('#hidden-kategori').val(uuid);
    console.log("📥 hidden-kategori diisi dengan:", uuid);

    lokasiSudahDiperiksa = true;
  }, err => {
    console.error(err);
    alert("Gagal mendapatkan lokasi: " + err.message);
  }, {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0
  });
}


function resetLocationInfo() {
  document.getElementById("distanceInfo").value = "";
  document.getElementById("distance-text").innerText = "";
  document.getElementById("kategori-absensi").value = "";
  document.getElementById("hidden-kategori").value = "";
  document.getElementById("status-absensi").value = "";
  console.log("❗ Lokasi kantor belum dipilih. Status dikosongkan.");
}


function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371000; // Radius bumi dalam meter
  const rad = Math.PI / 180;
  const φ1 = lat1 * rad;
  const φ2 = lat2 * rad;
  const Δφ = (lat2 - lat1) * rad;
  const Δλ = (lon2 - lon1) * rad;

  const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const jarak = R * c; // Jarak dalam meter

  console.log(`📍 Lokasi Kamu: ${lat1}, ${lon1}`);
  console.log(`📍 Lokasi Kantor: ${lat2}, ${lon2}`);
  console.log(`📏 Jarak Dihitung: ${jarak} meter`);

  return jarak;
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
    } else if (status === "Terlambat") {


      
        kategoriValue = "{{ optional($kategori_absensis->where('name', 'Terlambat')->first())->uuid }}";
    } else {
        kategoriValue = "{{ optional($kategori_absensis->where('name', 'Alpha / Tidak Hadir')->first())->uuid }}";
    }

    console.log("📌 UUID Kategori Absensi yang Dipilih:", kategoriValue);

    if (kategoriValue) {
        document.getElementById("kategori-absensi").value = kategoriValue;
        document.getElementById("hidden-kategori").value = kategoriValue;
        console.log("📥 hidden-kategori diisi lewat updateKategori:", kategoriValue);
    } else {
        console.warn("⚠️ UUID Kategori Absensi Tidak Ditemukan!");
    }
}




</script>