@extends('adminlte::page')

@section('title', 'User')

@section('content_header')
@stop

@section('content')

<div class="row" style="font-size: 15pt">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
                @if (have_permission('user_create'))
                <a href="{{ route('user.create') }}" class="btn btn-primary btn-md float-right"><i class="fas fa-plus"></i></a>
                @endif
            </div>
            <br>
            <div class="card-body">
                @include('user.filter')
                <br>
                <table id="datatable" class="table table-md table-hover dt-responsive nowrap" width="100%">
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
    <script src="{{ asset('js/page/page-user.js') }}" type="module"></script>
@stop
