
import {datatableHandleFetchData, datatableHandleDelete} from "/js/helper/datatable.js"
    $(function () {
        'use strict';
        var firstLoad = 0

        var stackColumnBarTransHistory;

        $('.chart-absensi-filter').on('change', function (e) {
            e.preventDefault()
            absensiDailyHistoryChart()
        })

        $('.chart-absensi-filter').on('change', function (e) {
            e.preventDefault()
            absensiDailyHistoryChart()
        })

        $('#top-employee-month').on('change', function (e) {
            e.preventDefault()
            fetchDataTopEmployee()
        })

        $('#top-employee-year').on('change', function (e) {
            e.preventDefault()
            fetchDataTopEmployee()
        })

        fetchDataTopEmployee()
        absensiDailyHistoryChart()

        $(document).ready(function() {
            $('.jumlah-cuti-filter').change(function() {
                let month = $('#jumlah-cuti-month').val();
                let year = $('#jumlah-cuti-year').val();
        
                $.ajax({
                    url: '/dashboard/jumlah-cuti/filter',
                    type: 'GET',
                    data: { month: month, year: year },
                    success: function(response) {
                        $('#jumlah-cuti').html(response.data.jumlah_cuti);
        
                        // Reset daftar cuti berdasarkan data response
                        let cutisHtml = '';
                        $.each(response.data.cutis, function(status, count) {
                            cutisHtml += `
                                <div class="row">
                                    <div class="col-8">
                                        <p>${status}</p>
                                    </div>
                                    <div class="col-4" style="text-align: center">
                                        <p>${count}</p>
                                    </div>
                                </div>`;
                        });
                        $('#cutis-container').html(cutisHtml);
                    },
                    error: function() {
                        alert('Error fetching data.');
                    }
                });
            });
        })

        function absensiDailyHistoryChart () {
            $('#chart-absensi-daily-history').empty();
            $('#chart-absensi-daily-history').hide();
            $('#chart-absensi-daily-loading').show();
    
            let query = new URLSearchParams({
                month: $('#chart-absensi-month').val(),
                year: $('#chart-absensi-year').val(),
            }).toString()
            $ajaxJsonGet({
                url: "/dashboard/absensi-daily-history?"+query,
                data: null,
                callBack: function (res) {
                    $('#chart-absensi-daily-loading').hide();
                    $('#chart-absensi-daily-history').show();

                    var options = stackColumnApexChart(res.xAxis, res.series, res.colors)
                    // console.log(options)
                    if (firstLoad == 0) {
                        stackColumnBarTransHistory = new ApexCharts(document.querySelector("#chart-absensi-daily-history"), options);
                        stackColumnBarTransHistory.render();
                        firstLoad++;
                    }else {
                        stackColumnBarTransHistory.updateOptions({
                            series: res.series,
                            yaxis: {},
                            xaxis: {
                                categories: res.xAxis,
                            },
                        })
                    }
                }
            })
        }

        function fetchDataTopEmployee() {
            let query = new URLSearchParams({
                month: $('#top-employee-month').val(),
                year: $('#top-employee-year').val(),
            }).toString();
            $('#datatable-top-employee').DataTable({
                destroy: true,
                responsive: true,
                serverSide: true,
                async: true,
                ordering: false,
                searching: false,
                processing: true,
                paginate: false,
                
                "pageLength": 100,
                "language": {
                    processing: '<div style="margin-top:-25px"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>'
                },
                "ajax": '/dashboard/top-employee/grid?' + query,
                columns: [
                    { title: "Nama Karyawan", data: 'nama_karyawan' },
                    { title: "WFO", data: 'WFO'},
                    { title: "WFH", data: 'WFH'},
                    { title: "Jumlah Poin", data: 'jumlah_point' },
                ],
            }, false, false);
        }

        function stackColumnApexChart (xAxisData = [], seriesData = [], colorsData = []) {

            var options = {
                series: seriesData,
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    stackType: '100%'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }],
                colors: colorsData,
                yaxis: {},
                xaxis: {
                    categories: xAxisData,
                },
                fill: {
                    opacity: 1
                },
                legend: {
                    position: 'right',
                    offsetX: 0,
                    offsetY: 50
                },
            };
    
            return options;
        }
});
