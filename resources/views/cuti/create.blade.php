@extends('adminlte::page')

@section('title', 'Create Cuti')

@section('content_header')
@stop

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<script>
fetch('/cuti/store', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': csrfToken
    }
})
.then(response => {
    if (response.status === 403) {
        return response.json().then(data => {
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: data.message
            });
        });
    }
})
.catch(error => {
    console.error('Kesalahan:', error);
});

</script>




