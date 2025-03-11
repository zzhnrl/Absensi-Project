@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @if(have_permission('dashboard_view'))
        <h1 class='text-light'>Dashboard</h1>
    @endif
@stop

@section('content')

@if(have_permission('dashboard_view'))
  <div class="row">
    <div class="col-12 col-md">
        <div class="card mx-auto card">
            <div class="card-header" style="height: 255px;align-content: center;">
                <h3 class="card-title w-100">
                <div class="row">
                    <div class="col-4 col-md">
                        <img src="{{ asset('img/logo/people.png') }}" width="150px">
                    </div>
                    <div class="col-4 col-md" style="align-content: center">
                        @foreach ($total_karyawan['roles'] as $key=>$role )
                          <div class="row">
                            <div class="col-8" style="text-align: left">
                              <p>{{ $key }}</p>
                            </div>
                            <div class="col-4" style="text-align: center">
                              <p>{{ $role }}</p>
                            </div>
                          </div>
                        @endforeach
                    </div>
                    <div class="col-4 col-md" style="align-content: center; text-align: center">
                        <p>Total Karyawan </p>
                        <h2><b>{{ $total_karyawan['jumlah_karyawan'] }}</b></h2>
                    </div>
                </div>
                </h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md">
        <div class="card mx-auto card">
            <div class="card-header">
                <h3 class="card-title w-100">
                  <div class="row">
                    <div class="col-12 col-md-4">
                      <label>Bulan</label>
                      <select id="jumlah-cuti-month" class="form-control jumlah-cuti-filter">
                          @foreach (App\Helpers\DateTime::getArrayOfMonths() as $index => $month)
                              <option value="{{ $index }}" @if ($index == (int) now()->format('m')) selected @endif>{{ $month }}</option>
                                  {{ $month }}
                              </option>
                          @endforeach
                      </select>                
                    </div>
                    <div class="col-12 col-md-4">
                        <label>Tahun</label>
                        <select id="jumlah-cuti-year" class="form-control jumlah-cuti-filter">
                            @for ($i=now()->format('Y');$i<=now()->addYears(5)->format('Y');$i++)
                            <option value="{{ $i }}" @if ($i == (int) now()->format('Y')) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                  </div>
                </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-4 col-md" style="align-self: center">
                    <img src="{{ asset('img/logo/leave.png') }}" width="150px">
                </div>
                <div class="col-4 col-md" style="align-content: center">
                  <div id="cutis-container">
                    @foreach ($total_cuti['cutis'] as $key => $cuti)
                        <div class="row">
                            <div class="col-8">
                                <p>{{ $key }}</p>
                            </div>
                            <div class="col-4" style="text-align: center">
                                <p>{{ $cuti }}</p>
                            </div>
                        </div>
                    @endforeach
                  </div>
                </div>
                <div class="col-4 col-md" style="align-content: center; text-align: center">
                    <p>Jumlah Cuti </p>
                    <h2><b id="jumlah-cuti">{{ $total_cuti['jumlah_cuti'] }}</b></h2>
                </div>
              </div>
            </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md">
        <div class="card mx-auto card">
            <div class="card-header">
              <h3 class="card-title w-100">
                  <h3>Grafik Absensi</h3>
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-3">
                  <label>Bulan</label>
                  <select id="chart-absensi-month" class="form-control chart-absensi-filter">
                      @foreach (App\Helpers\DateTime::getArrayOfMonths() as $index => $month)
                          <option value="{{ $index }}" @if ($index == (int) now()->format('m')) selected @endif>{{ $month }}</option>
                              {{ $month }}
                          </option>
                      @endforeach
                  </select>                
                </div>
                <div class="col-12 col-md-3">
                    <label>Tahun</label>
                    <select id="chart-absensi-year" class="form-control chart-absensi-filter">
                        @for ($i=now()->format('Y');$i<=now()->addYears(5)->format('Y');$i++)
                        <option value="{{ $i }}" @if ($i == (int) now()->format('Y')) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
              </div>
              <hr>
              <div id="chart-absensi-daily-loading" class="text-center hidden"><i class="fas fa-circle-notch fa-spin fa-5x"></i></div>
              <div id="chart-absensi-daily-history"></div>
            </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md">
        <div class="card mx-auto card">
            <div class="card-header">
              <h3 class="card-title w-100">
                <h3>Karyawan Teratas</h3>
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-md-3">
                  <label>Bulan</label>
                  <select id="top-employee-month" class="form-control top-employee-filter">
                      @foreach (App\Helpers\DateTime::getArrayOfMonths() as $index => $month)
                          <option value="{{ $month }}" @if ($month == now()->locale('id')->translatedFormat('F')) selected @endif>
                              {{ $month }}
                          </option>
                      @endforeach
                  </select>                
                </div>
                <div class="col-12 col-md-3">
                    <label>Tahun</label>
                    <select id="top-employee-year" class="form-control top-employee-filter">
                        @for ($i=now()->format('Y');$i<=now()->addYears(5)->format('Y');$i++)
                        <option value="{{ $i }}" @if ($i == (int) now()->format('Y')) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
              </div>
              <br>
              <table id="datatable-top-employee" class="table table-md table-hover dt-responsive nowrap" width="100%">
                <thead>
                </thead>
                <tbody>
                </tbody>
            </table>
            </div>
        </div>
    </div>
  </div>
@endif
@stop

@section('css')
@stop

@section('js')
<script>
    var options = {
          series: [{
            name: "Desktops",
            data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
        }],
          chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Product Trends by Month',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
    <script src="{{ asset('js/page/page-dashboard.js') }}" type="module"></script>
@stop