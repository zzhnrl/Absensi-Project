<style>
    .form-control {
        text-align: center;
    }
    textarea.text-center::placeholder {
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label class="w-100">Tanggal Mulai Cuti</label>
            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
        </div>

        <div class="form-group">
            <label class="w-100">Tanggal Akhir Cuti</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control">
        </div>

        <label class="w-100">Nama Karyawan</label>
        <div class="form-group text-center">
            <select class="select2 form-control text-center" name="user_uuid" id="user_uuid">
                <option value="">-- Pilih Nama Karyawan --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->uuid }}" name="user_uuid">{{ $user->userInformation->nama }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="nama_karyawan" id="nama_karyawan">

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
        // === SET TANGGAL REALTIME SECARA EXPLICIT ===
        function setTodayAsDefault() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayFormatted = `${yyyy}-${mm}-${dd}`;

            $('#tanggal_mulai').val(todayFormatted);
            $('#tanggal_akhir').val(todayFormatted);
        }

        setTodayAsDefault(); // Set saat pertama kali buka

        // === HITUNG TOTAL CUTI ===
        function hitungTotalCuti() {
            const tglMulai = new Date($('#tanggal_mulai').val());
            const tglAkhir = new Date($('#tanggal_akhir').val());

            if (!isNaN(tglMulai) && !isNaN(tglAkhir)) {
                let totalHari = (tglAkhir - tglMulai) / (1000 * 60 * 60 * 24) + 1;
                totalHari = totalHari > 0 ? totalHari : 1;

                $('#total_cuti').val(totalHari);
                $('#total_cuti_hidden').val(totalHari);
            } else {
                $('#total_cuti').val('');
                $('#total_cuti_hidden').val('');
            }
        }

        $('#tanggal_mulai, #tanggal_akhir').change(hitungTotalCuti);

        // === GET SISA CUTI ===
        $('#user_uuid').change(function () {
            const nama = $(this).find(':selected').text();
            $('#nama_karyawan').val(nama);

            const uuid = $(this).val();
            if (uuid) {
                $.ajax({
                    url: '/get-sisa-cuti/' + uuid,
                    type: 'GET',
                    success: function (res) {
                        $('#sisa_cuti').val(res.sisa_cuti);
                    },
                    error: function () {
                        $('#sisa_cuti').val('Gagal ambil data');
                    }
                });
            } else {
                $('#sisa_cuti').val('');
            }
        });

        $('form').submit(function () {
            $('#nama_karyawan').val($('#user_uuid').find(':selected').text());
            $('#total_cuti_hidden').val($('#total_cuti').val());
        });

        hitungTotalCuti(); // Hitung awal
    });
</script>
