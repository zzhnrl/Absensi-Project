@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
@php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
@php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
@stack('css')
@yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop

@section('body')
<div class="{{ $auth_type ?? 'login' }}-box">



    {{-- Card Box --}}
    <div class="card card-outline">

        {{-- Card Header --}}
        @hasSection('auth_header')
        <div class="card-header {{ config('adminlte.classes_auth_header', '') }} no-padding">
            <img src="{{ asset('img/logo/logo-tw.png') }}" height="100" class="center-image">

        </div>
        @endif

        {{-- Card Body --}}
        <div class="card-body   {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
            @yield('auth_body')
        </div>

        {{-- Card Footer --}}
        @hasSection('auth_footer')
        <div class="card-footer bg-dark rounded {{ config('adminlte.classes_auth_footer', '') }}">
            @yield('auth_footer')
        </div>
        @endif

    </div>

</div>
@stop

@section('adminlte_js')
@stack('js')
@yield('js')
@stop