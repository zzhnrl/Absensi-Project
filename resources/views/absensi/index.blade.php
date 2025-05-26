@extends('adminlte::page')

@section('title', 'Absensi')

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
                    use App\Models\IzinSakit;
                    use App\Models\Cuti;

                    $today       = Carbon::now('Asia/Jakarta')->toDateString(); // YYYY-MM-DD
                    $currentHour = Carbon::now('Asia/Jakarta')->hour;
                    $user        = Auth::user();

                    // Cek Izin Sakit hari ini
                    $hasSakit = IzinSakit::where('user_id', $user->id)
                                ->whereDate('tanggal', $today)
                                ->exists();

                    // Cek Cuti yang sudah approved dan periode-nya mencakup hari ini
                    $hasCuti = Cuti::where('user_id', $user->id)
                                ->where('status_cuti_id', '2')               // sesuaikan jika enum/numeric
                                ->whereDate('tanggal_mulai', '<=', $today)
                                ->whereDate('tanggal_akhir', '>=', $today)
                                ->exists();

                    // Jika salah satu true, tombol absensi disable
                    $disableCreate = $currentHour >= 17| $hasSakit || $hasCuti;
                @endphp

                @if (have_permission('absensi_create'))
                    <a 
                        href="{{ route('absensi.create') }}"
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
                <a href="{{ route('absensi.export.excel') }}" class="btn btn-success btn-md float-right mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @endif
            </div>
            <br>
            <div class="card-body">
                @include('absensi.filter')
                <br>
                <table id="datatable" class="table table-md table-hover dt-responsive nowrap" width="100%">
                    <thead class="thead-primary">
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    table {
        font-size: 18pt
    }
</style>
@stop

@section('js')
<script
    <script src="{{ asset('js/page/page-absensi.js') }}" type="module"></script>
@stop