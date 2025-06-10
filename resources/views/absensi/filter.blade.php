@php
    use Carbon\Carbon;
    $todayWIB = Carbon::now('Asia/Jakarta')->format('Y-m-d');
@endphp

<form method="GET" action="{{ route('absensi') }}">
    <div class="row">
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label>Date Filter</label>
                <input 
                    name="date_range"
                    type="text" 
                    class="flatpickr-daterange form-control" 
                    placeholder="Pick date range" 
                    id="absensi-date-filter" 
                    value="{{ old('date_range', request('date_range', $todayWIB . ' to ' . $todayWIB)) }}"
                    autocomplete="off"
                >
            </div>
        </div>
        <div class="col-12 col-md-3">
            <label>Karyawan</label>
            <select class="form-control form-select" name="karyawan_filter" id="karyawan-filter">
                <option value=''>-- Semua Data --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->uuid }}" {{ request('karyawan_filter') == $user->uuid ? 'selected' : '' }}>
                        {{ $user->userInformation->nama ?? $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label>Kategori Absensi</label>
            <select class="form-control form-select" name="kategori_filter" id="kategori-absensi-filter">
                <option value="">-- Semua Data --</option>
                {!! each_option($kategori_absensis, 'name', request('kategori_filter')) !!}
            </select>
        </div>
        <div class="col-12 col-md-3 mb-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>
