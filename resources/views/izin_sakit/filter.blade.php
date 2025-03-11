<div class="row">
    <div class="col-12 col-md-3">
        <div class="form-group">
            <label>Date Filter</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" id="izin-sakit-date-filter" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
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
</div>