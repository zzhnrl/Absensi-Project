@extends('adminlte::page')

@section('title', 'Point User')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif


                <a 
                  href="{{ route('history-point.index') }}" 
                  class="btn btn-info btn-md float-right mr-2"
                >
                  <i class="fas fa-history"></i> Riwayat Poin Saya
                </a>
      

                {{-- Tombol “Tambah Point User” --}}
                @if (have_permission('point_user_create'))
                <a href="{{ route('point_user.create') }}" class="btn btn-primary btn-md float-right">
                    <i class="fas fa-plus"></i>
                </a>
                @endif
            </div>
            <br>
            <div class="card-body">
                @include('point_user.filter')
                <br>
                <table id="datatable" class="table table-md table-hover dt-responsive nowrap" width="100%">
                    <thead class="thead-primary"></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    table {
        font-size: 18pt;
    }
</style>
@stop

@section('js')
<script src="{{ asset('js/page/page-point-user.js') }}" type="module"></script>
@stop
