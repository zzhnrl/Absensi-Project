@extends('adminlte::page')

@section('title', 'Create Cuti')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('cuti.store')}}" method="post" enctype="multipart/form-data">

                <div class="card-body">
                    @method('POST')
                    @csrf
                    @if(auth()->user()->userRole->role_id == 1)
                        @include('cuti.form_admin')
                    @else
                        @include('cuti.form_user')
                    @endif
                </div>

                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('cuti')}}" class="btn btn-default">
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