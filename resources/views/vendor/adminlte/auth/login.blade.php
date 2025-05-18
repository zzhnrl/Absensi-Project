@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', 'SEMESTA')

@section('auth_body')
<form action="{{ $login_url }}" method="post">
    @csrf

    {{-- Username field --}}
    <div class="row mb-3">
        <div class="col-12">
            <input
              type="email"
              name="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}"
              placeholder="Email"
              autofocus
            >
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    {{-- Password field with show/hide --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="input-group">
                <input
                  id="password"
                  type="password"
                  name="password"
                  class="form-control @error('password') is-invalid @enderror"
                  placeholder="{{ __('adminlte::adminlte.password') }}"
                >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <a href="#" id="togglePassword" style="color: inherit;">
                            <i class="fas fa-eye"></i>
                        </a>
                    </span>
                </div>
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Login button --}}
    <div class="row">
        <div class="col-5">
            <button
              type="submit"
              class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}"
            >
                <span class="fas fa-sign-in-alt"></span>
            </button>
        </div>
    </div>
</form>
@stop

@section('auth_footer')
{{-- Password reset link --}}
@if(isset($password_reset_url))
<!-- <p class="my-0">
    <a href="{{ $password_reset_url }}" class="text-white">
        {{ __('adminlte::adminlte.i_forgot_my_password') }}
    </a>
</p> -->
@endif

{{-- Register link --}}
@if(isset($register_url))
<p class="my-0">
    <a href="{{ $register_url }}">
        {{ __('adminlte::adminlte.register_a_new_membership') }}
    </a>
</p>
@endif
@stop

@section('adminlte_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('togglePassword');
    const pwd    = document.getElementById('password');
    if (!toggle || !pwd) return;

    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        // toggle type
        const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
        pwd.setAttribute('type', type);
        // toggle eye icon
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
</script>
@stop
