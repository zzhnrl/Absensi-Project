@extends('adminlte::page')

@section('title', 'Point User')

@section('content_header')
@stop


@section('content')
<div class="card">
<div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif


                
            </div>
    <div class="card-body table-responsive">
        <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Perubahan Poin</th>
                    <th>Total Poin</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
  // Kirim URL grid ke JS
  window.HISTORY_POINT_GRID_URL = "{{ route('history-point.grid') }}";
</script>
<script type="module" src="{{ asset('js/page/page-history-point.js') }}"></script>
@stop
