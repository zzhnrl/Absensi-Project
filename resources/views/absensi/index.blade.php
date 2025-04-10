@extends('adminlte::page')

@section('title', 'Absensi')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <a href="{{ route('absensi.export.excel') }}" class="btn btn-success btn-md float-right mr-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>

            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
                @if (have_permission('absensi_create'))
                <a href="{{ route('absensi.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
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