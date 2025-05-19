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
                @if (have_permission('izin_sakit_create'))
                    <a href="{{ route('izin_sakit.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
                @endif

                {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                @if (Auth::user()->role_id != 3)
                <a href="{{ route('rekap_izin_sakit.export') }}" class="btn btn-success btn-md float-right mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @endif

                @if ($breadcrumb) {!! $breadcrumb !!} @endif
                <!-- @if (have_permission('rekap_izin_sakit_create'))
                <a href="{{ route('rekap_izin_sakit.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
                @endif -->
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
<script
    <script src="{{ asset('js/page/page-izin-sakit.js') }}" type="module"></script>
@stop