<div class="row">
    <div class="col-12 col-md-3">
        <label>Bulan</label>
        <select id="rekap-izin-sakit-month" class="form-control rekap-izin-sakit-filter">
            @foreach (range(1,12) as $num)
                <option value="{{ $num }}" @if ($num == now()->month) selected @endif>
                    {{ \Carbon\Carbon::create()->month($num)->locale('id')->translatedFormat('F') }}
                </option>
            @endforeach
        </select>                
    </div>
    <div class="col-12 col-md-3">
        <label>Tahun</label>
        <select id="rekap-izin-sakit-year" class="form-control rekap-izin-sakit-filter">
            @for ($i = now()->year; $i <= now()->addYears(5)->year; $i++)
                <option value="{{ $i }}" @if ($i == now()->year) selected @endif>{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="col-12 col-md-3">
        <label>Karyawan</label>
        <select class="form-control rekap-izin-sakit-filter" id="karyawan-filter" name="karyawan-filter">
            <option value=''>-- Semua Data --</option>
            @foreach ($users as $user)
                <option value="{{ $user->uuid }}">{{ $user->userInformation->nama }}</option>
            @endforeach
        </select>
    </div>  

</div>

<table id="datatable" class="table table-striped mt-4" style="width: 100%"></table>
