@extends('adminlte::page')

@section('title', 'Rekap Absen')

@section('content_header')
@stop

@section('content')

<!-- <div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                @if (Auth::user()->role_id != 3)
                <a href="{{ route('rekap_izin_sakit.export') }}" class="btn btn-success btn-md float-right mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @endif

                @if ($breadcrumb) {!! $breadcrumb !!} @endif
                @if (have_permission('rekap_izin_sakit_create'))
                <a href="{{ route('rekap_izin_sakit.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
                @endif
            </div>
            <br>
            <div class="card-body">
                @include('rekap_izin_sakit.filter')
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
</div> -->
 <div class="row">
    <div class="col-12 col-md">
        <div class="card mx-auto card">
            <div class="card-header">
              <h3 class="card-title w-100">
                <h3>Karyawan Teratas</h3>
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-3">
                  <label>Bulan</label>
                  <select id="top-employee-month" class="form-control top-employee-filter">
                      @foreach (App\Helpers\DateTime::getArrayOfMonths() as $index => $month)
                          <option value="{{ $month }}" @if ($month == now()->locale('id')->translatedFormat('F')) selected @endif>
                              {{ $month }}
                          </option>
                      @endforeach
                  </select>                
                </div>
                <div class="col-12 col-md-3">
                    <label>Tahun</label>
                    <select id="top-employee-year" class="form-control top-employee-filter">
                        @for ($i=now()->format('Y');$i<=now()->addYears(5)->format('Y');$i++)
                        <option value="{{ $i }}" @if ($i == (int) now()->format('Y')) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
              </div>
              <br>
              <table id="datatable-top-employee" class="table table-md table-hover dt-responsive nowrap" width="100%">
                <thead>
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
    <script src="{{ asset('js/page/page-rekap-absen.js') }}" type="module"></script>
@stop