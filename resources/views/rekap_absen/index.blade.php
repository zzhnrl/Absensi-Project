@extends('adminlte::page')

@section('title', 'Rekap Absensi')

@section('content_header')
    <!-- <h1>Rekap Absensi</h1> -->
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            @if ($breadcrumb)
                {!! $breadcrumb !!}
            @endif

            <div class="row mt-2">
                <div class="col-md-3">
                    <select id="month-filter" class="form-control">
                        <option value="">-- Month --</option>
                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="year-filter" class="form-control">
                        <option value="">-- Tahun --</option>
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                    @if (Auth::user()->role_id != 3)
                        <button id="btn-export-pdf" class="btn btn-success btn-md text-white">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    @endif
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

        document.addEventListener("DOMContentLoaded", function() {
            const btnExportPdf = document.getElementById('btn-export-pdf');
            btnExportPdf.addEventListener('click', function() {
                const pdfParams = {
                    month: $('#month-filter').val(),
                    year: $('#year-filter').val(),
                };

                window.open(
                    `{{ route('rekap-absen.export.pdf') }}?${new URLSearchParams(pdfParams).toString()}`,
                    '_blank');
            });
        });
    </script>
    <script src="{{ asset('js/page/page-rekap-absen.js') }}" type="module"></script>
@stop
