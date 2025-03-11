@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
@php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
@php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
    class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
    class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    {{-- Small brand logo --}}
    <div class="d-flex align-items-center">
    <!-- Logo -->
        <img src="{{ asset('img/logo/logo-tw.png') }}"
            alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
            class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-3') }} bg-light"
            style="opacity:1; width: 50px; height: 50px;">

        <!-- Brand Text -->
        <span class="ml-3 font-weight-bold text-white" style="font-size: 1.2rem;">
            {{ config('adminlte.logo_text', 'ABSENSI') }}
        </span>
    </div>

</a>
<br>