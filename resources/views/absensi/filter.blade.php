<div class="row">
    <div class="col-12 col-md-3">
        <div class="form-group">
            <label>Date Filter</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" id="absensi-date-filter" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>
    </div>
    <div class="col-12 col-md-3">
        <label>Karyawan</label>
        <select class="group-filter form-control form-select" id="karyawan-filter" name="karyawan-filter">
            <option value=''>-- Semua Data --</option>" : ""
            @foreach ($users as $user)
                <option value={{ $user->uuid }}>{{ $user->userInformation->nama }}</option>
            @endforeach
            {{-- {!! each_option($users, 'name', null) !!} --}}
        </select>
    </div>
    <div class="col-12 col-md-3">
        <label>Kategori Absensi</label>
        <select class="group-filter form-control form-select" id="kategori-absensi-filter" name="kategori-absensi-filter">
            {!! each_option($kategori_absensis, 'name', null) !!}
        </select>
    </div>
</div>
