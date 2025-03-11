@extends('adminlte::page')

@section('title', 'Create Status Cuti')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('status_cuti.store')}}" method="post" enctype="multipart/form-data">

                <div class="card-body">
                    @method('POST')
                    @csrf
                    @include('status_cuti.form')
                </div>

                <div class="card-footer">
                    <div class="float-right">
                        <a href="{{route('status_cuti')}}" class="btn btn-default">
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
<script>
    const preview_image = $('#preview-image')
    const upload_image = $('#upload-image')
    $(document).ready(function() {
        $('select').select2();
    });

    upload_image.on('change', function(e) {
        readURL(this)
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview_image.attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@stop