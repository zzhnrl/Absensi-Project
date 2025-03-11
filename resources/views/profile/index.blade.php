@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class='card-header'>
                @if ($breadcrumb) {!! $breadcrumb !!} @endif
            </div>
            <form action="{{route('profile.update',[$user->uuid])}}" method="POST" enctype="multipart/form-data">
                <div class='card-body'>
                    @method('PUT')
                    @csrf
                    @include('profile.form')
                </div>
                <div class="card-footer ">
                    <button type="submit" id="submit-data" class="btn btn-primary float-right">Simpan</button>
                    <a href="{{route('profile')}}" class="btn btn-default float-right">
                        Batal
                    </a>
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

        upload_image.on('change', function (e) {
            readURL(this)
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    preview_image.attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
@stop
