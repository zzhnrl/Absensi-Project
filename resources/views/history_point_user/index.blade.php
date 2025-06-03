@extends('adminlte::page')

@section('title', 'Point User')

@section('content_header')
@stop

@section('content')
<div class="card">
    <div class="card-header">
        {{-- Breadcrumb --}}
        @if ($breadcrumb)
            <div class="mb-3">
                {!! $breadcrumb !!}
            </div>
        @endif

        {{-- Filter dan Export --}}
        <div class="d-flex flex-wrap align-items-end justify-content-between">
            <form id="filter-form" class="form-inline mb-2">
                <div class="form-group mr-2 mb-2">
                    <label for="month" class="mr-2 font-weight-bold">Bulan:</label>
                    <select class="form-control" name="month" id="month">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="year" class="mr-2 font-weight-bold">Tahun:</label>
                    <select class="form-control" name="year" id="year">
                        @foreach (range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Tampilkan</button>
            </form>

            <a id="export-excel" class="btn btn-success mb-2 ml-md-2" href="{{ route('history-point.export') }}">
    <i class="fas fa-file-excel mr-1"></i> Export Excel
</a>

        </div>
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
    window.HISTORY_POINT_GRID_URL = "{{ route('history-point.grid') }}";
</script>
<script type="module" src="{{ asset('js/page/page-history-point.js') }}"></script>
@stop
