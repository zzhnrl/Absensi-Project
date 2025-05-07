<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="form-control" name="date" 
                   id="absensi-date" value="{{ now()->format('Y-m-d') }}" disabled>
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
            <label>Kategori (Otomatis)</label>
            <select id="kategori-absensi" class="select2 form-control" disabled>
                @foreach ($kategori_absensis as $kategori)
                    <option value="{{ $kategori->uuid }}" data-name="{{ $kategori->name }}">{{ $kategori->name }}</option>
                @endforeach
            </select>
            <!-- Input hidden agar data tetap dikirim ke backend -->
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

    let statusAbsensi = "Alpha / Tidak Hadir";
    if (hours >= 9 && hours <= 10) {
        statusAbsensi = "Hadir";
    } else if (hours >= 10 && hours < 17) {
        statusAbsensi = "Terlambat";
    }

    document.getElementById("status-absensi").value = statusAbsensi;
    console.log(`📌 Status Absensi: ${statusAbsensi}`);

    // Tunggu lokasi sebelum update kategori
}

function checkLocation() {
    if (!navigator.geolocation) {
        document.getElementById("status").innerText = "❌ Geolocation tidak didukung.";
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;

        const officeLat = parseFloat("{{ $office->latitude ?? 0 }}");
        const officeLng = parseFloat("{{ $office->longitude ?? 0 }}");

        console.log(`📍 Lokasi Pengguna: Lat ${userLat}, Lng ${userLng}`);

        if (!officeLat || !officeLng) {
            document.getElementById("status").innerText = "❌ Lokasi kantor belum diatur.";
            return;
        }

        const distance = getDistance(userLat, userLng, officeLat, officeLng);
        console.log(`📏 Jarak ke Kantor: ${distance} meter`);

        document.getElementById("distanceInfo").innerText = `Jarak ke kantor: ${distance.toFixed(2)} meter`;

        let kategoriUUID = pilihKategori(distance);
        if (kategoriUUID) {
            document.getElementById("kategori-absensi").value = kategoriUUID;
            document.getElementById("hidden-kategori").value = kategoriUUID;
        }

        console.log(`📌 Kategori Absensi Terpilih: ${kategoriUUID}`);
    });
}

function pilihKategori(jarak) {
    let kategoriDropdown = document.getElementById("kategori-absensi");

    let kategoriAbsensi = "WFH"; // Default jika jauh dari kantor lebih dari 100 meter
    if (jarak < 100) { 
        kategoriAbsensi = "WFO";
    }

    console.log(`📌 Kategori Berdasarkan Jarak: ${kategoriAbsensi}`);

    let selectedUUID = "";

    // Loop kategori untuk mencari UUID yang cocok dengan kategori yang dipilih
    for (let option of kategoriDropdown.options) {
        if (option.getAttribute("data-name") === kategoriAbsensi) {
            selectedUUID = option.value;
            break;
        }
    }

    return selectedUUID;
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
    } else {
        console.warn("⚠️ UUID Kategori Absensi Tidak Ditemukan!");
    }
}


// Fungsi untuk menghitung jarak antara dua koordinat (Haversine formula)
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Radius bumi dalam meter
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}


</script>
