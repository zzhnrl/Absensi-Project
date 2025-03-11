@extends('adminlte::page')

@section('title', 'Create Rekap Izin Sakit')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('rekap_izin_sakit.store')}}" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    @method('POST')
                    @csrf
                    @include('rekap_izin_sakit.form')
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('rekap_izin_sakit')}}" class="btn btn-default">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
    <script src="{{ asset('js/page/page-rekap-izin-sakit-create.js') }}"></script>
@stop
