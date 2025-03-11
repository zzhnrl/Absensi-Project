<div class="row">
    <div class="col-12 col-md-3">
      <label>Bulan</label>
      <select id="point-user-month" class="form-control point-user-filter">
          @foreach (App\Helpers\DateTime::getArrayOfMonths() as $index => $month)
              <option value="{{ $month }}" @if ($month == now()->locale('id')->translatedFormat('F')) selected @endif>
                  {{ $month }}
              </option>
          @endforeach
      </select>                
    </div>
    <div class="col-12 col-md-3">
        <label>Tahun</label>
        <select id="point-user-year" class="form-control point-user-filter">
            @for ($i=now()->format('Y');$i<=now()->addYears(5)->format('Y');$i++)
            <option value="{{ $i }}" @if ($i == (int) now()->format('Y')) selected @endif>{{ $i }}</option>
            @endfor
        </select>
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