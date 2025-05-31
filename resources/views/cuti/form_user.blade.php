<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    input.form-control,
    textarea.form-control {
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-12">
        {{-- Tanggal Mulai --}}
        <div class="form-group position-relative">
            <label>Tanggal Mulai Cuti</label>
            <div class="input-group">
                <input type="text" id="tanggal_mulai" name="tanggal_mulai" class="form-control flatpickr" placeholder="Pilih tanggal">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        {{-- Tanggal Akhir --}}
        <div class="form-group position-relative">
            <label>Tanggal Akhir Cuti</label>
            <div class="input-group">
                <input type="text" id="tanggal_akhir" name="tanggal_akhir" class="form-control flatpickr" placeholder="Pilih tanggal">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        {{-- Nama --}}
        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control text-center" value="{{ auth()->user()->userInformation->nama }}" disabled>
            <input type="hidden" name="nama_karyawan" value="{{ auth()->user()->userInformation->nama }}">
        </div>

{{-- … --}}
{{-- Jenis Cuti --}}
<div class="form-group">
    <label>Jenis Cuti</label>
    <select name="jenis_cuti" id="jenis_cuti" class="form-control" required>
        <option value="">-- Pilih Jenis Cuti --</option>
        <option value="tahunan">Cuti Tahunan</option>
        <!-- <option value="sakit">Cuti Sakit</option> -->
        <option value="alasan_penting">Cuti Karena Alasan Penting</option>
        <option value="besar">Cuti Besar</option>
        <option value="melahirkan">Cuti Melahirkan</option>
        <option value="diluar_tanggungan">Cuti Diluar Tanggungan Negara</option>
    </select>
</div>

{{-- Kuota Cuti --}}
<div class="form-group" id="kuota_cuti_group">
    <label>Kuota Cuti</label>
    <input type="text" id="sisa_cuti"
           class="form-control text-center"
           value="{{ auth()->user()->sisa_cuti ?? 'Tidak tersedia' }}"
           disabled>
</div>
{{-- … --}}


        {{-- Total Cuti --}}
        <div class="form-group">
            <label>Total Cuti</label>
            <input type="text" id="total_cuti" class="form-control text-center" value="0" disabled>
            <input type="hidden" name="total_cuti" id="total_cuti_hidden">
        </div>

        {{-- Keterangan --}}
        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control text-center" placeholder="Masukkan keterangan tambahan" name="keterangan">{{ old('keterangan') }}</textarea>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi Flatpickr
    const mulaiPicker = flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d", defaultDate: "today", allowInput: true
    });
    const akhirPicker = flatpickr("#tanggal_akhir", {
        dateFormat: "Y-m-d", defaultDate: "today", allowInput: true
    });
    document.querySelector("#tanggal_mulai + .input-group-append").addEventListener("click", () => mulaiPicker.open());
    document.querySelector("#tanggal_akhir + .input-group-append").addEventListener("click", () => akhirPicker.open());

    // Cache tanggal merah supaya gak fetch berulang
    let tanggalMerahCache = null;

    async function fetchTanggalMerah() {
        if (tanggalMerahCache) return tanggalMerahCache;

        try {
            const res = await fetch('https://api-harilibur.vercel.app/api');
            if (!res.ok) throw new Error('Gagal fetch tanggal merah');

            const data = await res.json();

            // Ambil tanggal merah dalam format YYYY-MM-DD array
            tanggalMerahCache = data.map(d => d.holiday_date);
            
            // Bisa tambah cuti bersama manual jika perlu
            // tanggalMerahCache.push('2025-05-30');

            return tanggalMerahCache;
        } catch (e) {
            console.error(e);
            // Kalau error, anggap tidak ada tanggal merah agar tidak blocking
            return [];
        }
    }

    async function hitungCuti() {
        const startVal = document.getElementById("tanggal_mulai").value;
        const endVal = document.getElementById("tanggal_akhir").value;
        const totalCutiInput = document.getElementById("total_cuti");
        const totalCutiHidden = document.getElementById("total_cuti_hidden");

        if (!startVal || !endVal) {
            totalCutiInput.value = 0;
            totalCutiHidden.value = 0;
            return;
        }

        const start = new Date(startVal);
        const end = new Date(endVal);

        if (isNaN(start) || isNaN(end) || end < start) {
            totalCutiInput.value = 0;
            totalCutiHidden.value = 0;
            return;
        }

        const tanggalMerah = await fetchTanggalMerah();

        let totalDays = 0;
        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const day = d.getDay(); // 0 Minggu, 6 Sabtu
            const dateString = d.toISOString().slice(0, 10);

            if (day !== 0 && day !== 6 && !tanggalMerah.includes(dateString)) {
                totalDays++;
            }
        }

        totalCutiInput.value = totalDays;
        totalCutiHidden.value = totalDays;
    }

    document.getElementById("tanggal_mulai").addEventListener("change", hitungCuti);
    document.getElementById("tanggal_akhir").addEventListener("change", hitungCuti);

    // Hitung sekali saat load (kalau ada default tanggal)
    hitungCuti();

    // Show/hide kuota cuti untuk jenis "tahunan"
    const jenisSelect = document.getElementById("jenis_cuti");
    const kuotaGroup = document.getElementById("kuota_cuti_group");
    jenisSelect.addEventListener("change", function () {
        kuotaGroup.style.display = (this.value === "tahunan") ? "" : "none";
    });
});
</script>


 