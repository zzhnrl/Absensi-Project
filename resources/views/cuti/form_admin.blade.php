<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Cuti</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .form-control {
        text-align: center;
    }
    textarea.text-center::placeholder {
        text-align: center;
    }
    </style>
</head>
<body>

<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="w-100">Tanggal Mulai Cuti</label>
            <div class="input-with-icon">
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
                <span class="icon-calendar" onclick="document.getElementById('tanggal_mulai').showPicker?.() || document.getElementById('tanggal_mulai').focus()">
                    
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="w-100">Tanggal Akhir Cuti</label>
            <div class="input-with-icon">
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control">
                <span class="icon-calendar" onclick="document.getElementById('tanggal_akhir').showPicker?.() || document.getElementById('tanggal_akhir').focus()">
                    
                </span>
            </div>
        </div>

        <label class="w-100">Nama Karyawan</label>
        <div class="form-group text-center">
        <select class="select2 form-control text-center" name="user_uuid" id="user_uuid">
    <option value="">-- Pilih Nama Karyawan --</option>
    @foreach ($users as $user)
        <option 
            value="{{ $user->uuid }}" 
            data-sisa_cuti="{{ $user->sisa_cuti }}"
        >
            {{ $user->userInformation->nama }}
        </option>
    @endforeach
</select>

        </div>

        <input type="hidden" name="nama_karyawan" id="nama_karyawan">

        <div class="form-group">
            <label >Jenis Cuti</label>
            <select class="form-control" name="jenis_cuti" id="jenis_cuti" required>
                <option value="">-- Pilih Jenis Cuti --</option>
                <option value="tahunan">Cuti Tahunan</option>
                <option value="alasan_penting">Cuti Karena Alasan Penting</option>
                <option value="besar">Cuti Besar</option>
                <option value="melahirkan">Cuti Melahirkan</option>
                <option value="diluar_tanggungan">Cuti Diluar Tanggungan Negara</option>
            </select>
        </div>

        <div class="form-group">
            <label>Total Cuti</label>
            <input type="text" id="total_cuti" class="form-control" value="0" disabled>
            <input type="hidden" name="total_cuti" id="total_cuti_hidden">
        </div>

        <div class="form-group">
    <label class="w-100">Sisa Cuti</label>
    <input type="text" id="sisa_cuti" class="form-control" readonly>
</div>


        <div class="form-group">
            <label class="w-100">Keterangan</label>
            <textarea class="form-control text-center" style="height: 100px; padding-top: 30px;" name="keterangan" placeholder="Masukkan keterangan tambahan">{{ old('keterangan') }}</textarea>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#user_uuid').select2();

    const tanggalMulai = document.getElementById("tanggal_mulai");
    const tanggalAkhir = document.getElementById("tanggal_akhir");
    const totalCutiInput = document.getElementById("total_cuti");
    const totalCutiHidden = document.getElementById("total_cuti_hidden");
    const sisaCutiInput = $('#sisa_cuti');
    const namaKaryawanInput = $('#nama_karyawan');

    let tanggalMerahCache = null;

    async function fetchTanggalMerah() {
        if (tanggalMerahCache) return tanggalMerahCache;

        try {
            const res = await fetch("https://api-harilibur.vercel.app/api");
            if (!res.ok) throw new Error("Gagal fetch tanggal merah");

            const data = await res.json();
            tanggalMerahCache = data.map(d => d.holiday_date);
            return tanggalMerahCache;
        } catch (e) {
            console.error(e);
            return [];
        }
    }

    async function hitungCuti() {
        const startVal = tanggalMulai.value;
        const endVal = tanggalAkhir.value;

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
            const day = d.getDay();
            const dateString = d.toISOString().slice(0, 10);

            if (day !== 0 && day !== 6 && !tanggalMerah.includes(dateString)) {
                totalDays++;
            }
        }

        totalCutiInput.value = totalDays;
        totalCutiHidden.value = totalDays;
    }

    tanggalMulai.addEventListener("change", hitungCuti);
    tanggalAkhir.addEventListener("change", hitungCuti);

    // Gunakan jQuery event handler untuk select2
    $('#user_uuid').on('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const sisaCuti = selectedOption.getAttribute("data-sisa_cuti") || 0;
        sisaCutiInput.val(sisaCuti);

        const namaKaryawan = selectedOption.textContent.trim();
        namaKaryawanInput.val(namaKaryawan);
    });

    hitungCuti();

    if ($('#user_uuid').val() !== "") {
        $('#user_uuid').trigger('change');
    }
});


</script>

</body>
</html>
