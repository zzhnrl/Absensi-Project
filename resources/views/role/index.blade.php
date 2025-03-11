@extends('adminlte::page')

@section('title', 'Role')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
                @if (have_permission('role_create'))
                <a href="{{ route('role.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
                @endif
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
    <script src="{{ asset('js/page/page-role.js') }}" type="module"></script>
@stop
