<div class="row">
    <div class="col-12 col-md-3">
        <div class="form-group">
            <label>Date Filter</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" id="cuti-date-filter">
        </div>
    </div>
    <div class="col-12 col-md-3">
        <label>Karyawan</label>
        <select class="group-filter form-control form-select" id="karyawan-filter" name="karyawan-filter">
            <option value=''>-- Semua Data --</option>" : ""
            @foreach ($users as $user)
                <option value={{ $user->uuid }}>{{ $user->userInformation->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-3">
        <label>Status</label>
        <select class="group-filter form-control form-select" id="status-filter" name="status-filter">
            {!! each_option($status_cutis, 'nama', null) !!}
        </select>
    </div>  
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Ambil tanggal hari ini dalam format YYYY-MM-DD
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayFormatted = `${yyyy}-${mm}-${dd}`;

        // Inisialisasi flatpickr dengan default value range hari ini
        flatpickr("#cuti-date-filter", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [todayFormatted, todayFormatted]
        });
    });
</script>
