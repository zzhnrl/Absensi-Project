@extends('adminlte::page')

@section('title', 'Rekap Absensi')

@section('content_header')
    <!-- <h1>Rekap Absensi</h1> -->
@stop

@section('content')
<div class="card">
  <div class="card-header">
    @if($breadcrumb) {!! $breadcrumb !!} @endif

    <div class="row mt-2">
      <div class="col-md-3">
        <select id="month-filter" class="form-control">
          <option value="">-- Bulan --</option>
          @foreach(\App\Helpers\DateTime::getArrayOfMonths() as $m)
            <option value="{{ $m }}">{{ $m }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select id="year-filter" class="form-control">
          <option value="">-- Tahun --</option>
          @for($y = now()->year; $y >= now()->year - 5; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
          @endfor
        </select>
      </div>
    </div>
  </div>

  <div class="card-body">
    <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap">
      <thead>
        <tr>
          <th>No</th>
          <th>Karyawan</th>
          <th>WFO</th>
          <th>WFH</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
@stop

@section('js')
<script>
  window.REKAP_ABSEN_DATA_URL = "{{ route('rekap-absen.data') }}";
</script>
<script src="{{ asset('js/page/page-rekap-absen.js') }}" type="module"></script>
@stop
