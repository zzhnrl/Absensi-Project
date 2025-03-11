@extends('adminlte::page')

@section('title', 'Permission')

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
                <input type="hidden" value="{{ $role->uuid }}" id ="role-uuid">
                <label>Permission Configuration Settings for {{ $role->name }}</label>
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
@stop

@section('js')
    <script src="{{ asset('js/page/page-permission.js') }}" type="module"></script>
@stop
