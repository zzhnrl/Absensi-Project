@extends('adminlte::page')

@section('title', 'Rekap Izin Sakit')

@section('content_header')
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class='card-header'>
                    {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                    @if (Auth::user()->role_id != 3)
                        <button id="btn-export-pdf" class="btn btn-success btn-md float-right mr-2">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                    @endif

                    @if ($breadcrumb)
                        {!! $breadcrumb !!}
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
    <script src="{{ asset('js/page/page-rekap-izin-sakit.js') }}" type="module"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnExportPdf = document.getElementById('btn-export-pdf');
            btnExportPdf.addEventListener('click', function() {
                const pdfParams = {
                    month: $('#rekap-izin-sakit-month').val(),
                    year: $('#rekap-izin-sakit-year').val(),
                    user_uuid: $('#karyawan-filter').val(),
                    // search_param: $('#datatable_filter input').val(),
                };

                window.open(
                    `{{ route('rekap-izin-sakit.export.pdf') }}?${new URLSearchParams(pdfParams).toString()}`,
                    '_blank');
            });
        });
    </script>
@stop
