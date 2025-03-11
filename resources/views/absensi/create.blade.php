@extends('adminlte::page')

@section('title', 'Create Absensi')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('absensi.store')}}" method="post" enctype="multipart/form-data">

                <div class="card-body">
                    @method('POST')
                    @csrf
                    @if(auth()->user()->userRole->role_id == 1)
                        @include('absensi.form_admin')
                    @else
                        @include('absensi.form_user')
                    @endif
                </div>

                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('absensi')}}" class="btn btn-default">
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