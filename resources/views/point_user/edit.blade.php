@extends('adminlte::page')

@section('title', 'Edit Point User')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('point_user.update',[$point_user->uuid])}}" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    @method('PUT')
                    @csrf
                    @include('point_user.form')
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('point_user')}}" class="btn btn-default">
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