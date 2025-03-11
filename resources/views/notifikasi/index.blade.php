@extends('adminlte::page')

@section('title', 'Notifikasi')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <br>
            <div class="card-body">
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
    <script src="{{ asset('js/page/page-notifikasi.js') }}" type="module"></script>
@stop
