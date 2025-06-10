@extends('adminlte::page')

@section('title', 'Izin Sakit')

@section('content_header')
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif

                @php
                    use Carbon\Carbon;
                    use App\Models\Absensi;
                    use App\Models\Cuti;
                    use App\Models\IzinSakit;

                    $today       = Carbon::now('Asia/Jakarta')->toDateString(); // YYYY-MM-DD WIB
                    $user        = Auth::user();

                    // 1) Sudah absen hari ini?
                    $hasAbsensi  = Absensi::where('user_id',   $user->id)
                                         ->whereDate('tanggal', $today)
                                         ->exists();

                    // 2) Sedang dalam cuti yang sudah approved dan hari ini masih di antara awal–akhir
                    $hasCuti     = Cuti::where('user_id',      $user->id)
                    ->where('status_cuti_id', '2')               // sesuaikan jika enum/numeric
                                ->whereDate('tanggal_mulai', '<=', $today)
                                ->whereDate('tanggal_akhir', '>=', $today)
                                ->exists();

                    // 3) Sudah pernah ajukan izin sakit hari ini?
                    $hasSakit    = IzinSakit::where('user_id',   $user->id)
                                           ->whereDate('tanggal', $today)
                                           ->exists();

                    // Disable jika salah satu true
                    $disableCreate = $hasAbsensi || $hasCuti || $hasSakit;
                @endphp

                @if (have_permission('izin_sakit_create'))
                    <a 
                        id="btn-absen"
                        href="{{ route('izin_sakit.create') }}"
                        class="btn btn-primary btn-md float-right {{ $disableCreate ? 'disabled' : '' }}"
                        @if($disableCreate)
                          aria-disabled="true"
                          onclick="return false;"
                        @endif
                    >
                      <i class="fas fa-plus"></i>
                    </a>
                @endif

                {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                @if (Auth::user()->role_id != 3)
                    <a href="{{ route('rekap_izin_sakit.export') }}" class="btn btn-success btn-md float-right mr-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                @endif
            </div>

            <br>
            <div class="card-body">
                @include('izin_sakit.filter')
                <br>
                <table id="datatable" class="table table-md table-hover dt-responsive nowrap" width="100%">
                    <thead class="thead-primary"></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    table { font-size: 18pt; }
</style>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnAbsen = document.getElementById('btn-absen');
        const today = new Date();
        const isoToday = today.toISOString().split('T')[0];
        const dayOfWeek = today.getDay(); // 0 = Minggu, 6 = Sabtu

        // Disable jika weekend (Sabtu/Minggu)
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            disableButtonAbsen();
        } else {
            // Jika bukan weekend, cek hari libur nasional via API
            fetch('https://api-harilibur.vercel.app/api')
                .then(response => response.json())
                .then(data => {
                    const isHoliday = data.some(item => item.holiday_date === isoToday);
                    if (isHoliday) {
                        disableButtonAbsen();
                    }
                })
                .catch(error => {
                    console.error('Gagal cek hari libur nasional:', error);
                });
        }

        function disableButtonAbsen() {
            if (btnAbsen) {
                btnAbsen.classList.add('disabled');
                btnAbsen.setAttribute('aria-disabled', 'true');
                btnAbsen.onclick = () => false;
            }
        }
    });
</script>

<script src="{{ asset('js/page/page-izin-sakit.js') }}" type="module"></script>
@stop
