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
                {{-- Tombol “Export Excel” hanya untuk role_id ≠ 3 --}}
                @if (Auth::user()->role_id != 3)
                <a href="{{ route('user.export') }}" class="btn btn-success btn-md float-right mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @endif

            </div>
            <br>
            <div class="card-body">
                @include('user.filter')
                <br>
                <table id="datatable" class="table table-md table-hover dt-responsive nowrap" width="100%" data-role-id="{{ auth()->user()->role_id }}">
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