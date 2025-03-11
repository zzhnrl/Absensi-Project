@extends('adminlte::page')

@section('title', 'Create Kategori Absensi')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('kategori_absensi.store')}}" method="post" enctype="multipart/form-data">

                <div class="card-body">
                    @method('POST')
                    @csrf
                    @include('kategori_absensi.form')
                </div>

                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('kategori_absensi')}}" class="btn btn-default">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-data">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')

@stop