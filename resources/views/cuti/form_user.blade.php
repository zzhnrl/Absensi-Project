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
        <div class="form-group position-relative">
            <label>Tanggal Mulai Cuti</label>
            <div class="input-group">
                <input type="text" id="tanggal_mulai" name="tanggal_mulai" class="form-control flatpickr" placeholder="Pilih tanggal">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        <div class="form-group position-relative">
            <label>Tanggal Akhir Cuti</label>
            <div class="input-group">
                <input type="text" id="tanggal_akhir" name="tanggal_akhir" class="form-control flatpickr" placeholder="Pilih tanggal">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control text-center" name="nama_karyawan" value="{{ auth()->user()->userInformation->nama }}" disabled>
            <input type="hidden" class="form-control" name="nama_karyawan" value="{{ auth()->user()->userInformation->nama }}">
        </div>

        <div class="form-group">
            <label>Sisa Cuti</label>
            <input type="text" class="form-control text-center" value="{{ auth()->user()->sisa_cuti ?? 'Tidak tersedia' }}" disabled>
        </div>

        <div class="form-group">
            <label>Total Cuti</label>
            <input type="text" id="total_cuti" class="form-control text-center" value="0" disabled>
            <input type="hidden" name="total_cuti" id="total_cuti_hidden">
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control text-center" placeholder="Masukkan keterangan tambahan" name="keterangan">{{ old('keterangan') }}</textarea>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const mulaiPicker = flatpickr("#tanggal_mulai", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            allowInput: true
        });

        const akhirPicker = flatpickr("#tanggal_akhir", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            allowInput: true
        });

        document.querySelector("#tanggal_mulai").nextElementSibling.addEventListener("click", () => mulaiPicker.open());
        document.querySelector("#tanggal_akhir").nextElementSibling.addEventListener("click", () => akhirPicker.open());

        function hitungCuti() {
            let startDate = new Date(document.getElementById("tanggal_mulai").value);
            let endDate = new Date(document.getElementById("tanggal_akhir").value);
            let totalCuti = document.getElementById("total_cuti");
            let totalCutiHidden = document.getElementById("total_cuti_hidden");

            if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                let totalHari = 0;
                if (startDate.toDateString() === endDate.toDateString()) {
                    totalHari = 1;
                } else {
                    let selisihWaktu = endDate - startDate;
                    totalHari = (selisihWaktu / (1000 * 60 * 60 * 24)) + 1;
                    totalHari = totalHari > 0 ? totalHari : 1;
                }
                totalCuti.value = totalHari;
                totalCutiHidden.value = totalHari;
            } else {
                totalCuti.value = '';
                totalCutiHidden.value = '';
            }
        }

        document.getElementById("tanggal_mulai").addEventListener("change", hitungCuti);
        document.getElementById("tanggal_akhir").addEventListener("change", hitungCuti);

        setTimeout(hitungCuti, 300);
    });
</script>
