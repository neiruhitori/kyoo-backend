@extends('layouts.app')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Admin Kyoo Dashboard') }}</h1>
</div>
<div class="row">
    <div class="col-md-12">
        @include('layouts.alert')
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Branches')
                            }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalBranch)}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Customer')
                            }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalUser)}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ __('Total Appointment') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalAppointment)}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ __('Total Direct Queue') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalOnsite)}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ __('Total Exhibition') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{number_format($totalExhibition)}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12 col-lg-7">
        <!-- Area Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Transaction Performance') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col text-right">
                        <a href="{{ route('admin.export') }}" class="btn btn-primary">{{ __('Download Report') }}</a>
                    </div>
                </div>
                <div class="chart-area">
                    <div class="chartjs-size-monitor">
                        <div class="chartjs-size-monitor-expand">
                            <div class=""></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink">
                            <div class=""></div>
                        </div>
                    </div>
                    <canvas id="myAreaChart" style="display: block; height: 320px; width: 387px;" width="774"
                        height="640" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('admin/vendor/chart.js/Chart.min.js')}}"></script>
<script>
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    const appointments = JSON.parse('{!! $appointmentGraph !!}')
    const onsites = JSON.parse('{!! $onsiteGraph !!}')
    const exhibitions = JSON.parse('{!! $exhibitionGraph !!}')

    const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des"];

    const appointmentData = []
    const onsiteData = []
    const exhibitionData = []

    let iA = 0, iO = 0, iE = 0
    for (let i = 0; i <= new Date().getMonth(); i++) {
        if (appointments.length > iA && appointments[iA].month == i + 1) {
            appointmentData[i] = appointments[iA].total
            iA++
        } else {
            appointmentData[i] = 0
        }

        if (onsites.length > iO && onsites[iO].month == i + 1) {
            onsiteData[i] = onsites[iO].total
            iO++
        } else {
            onsiteData[i] = 0
        }

        if (exhibitions.length > iE && exhibitions[iE].month == (i + 1)) {
            exhibitionData[i] = exhibitions[iE].total
            iE++
        } else {
            exhibitionData[i] = 0
        }
    }

    console.log(exhibitionData)

    var ctx = document.getElementById("myAreaChart");

    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: "Total Appointment",
                    lineTension: 0.3,
                    backgroundColor: "rgba(40, 167, 69, 0.05)",
                    borderColor: "rgba(40, 167, 69, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(40, 167, 69, 1)",
                    pointBorderColor: "rgba(40, 167, 69, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(40, 167, 69, 1)",
                    pointHoverBorderColor: "rgba(40, 167, 69, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: appointmentData,
                },
                {
                    label: "Total Antrian Onsite",
                    lineTension: 0.3,
                    backgroundColor: "rgba(220, 53, 69, 0.05)",
                    borderColor: "rgba(220, 53, 69, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointBorderColor: "rgba(220, 53, 69, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointHoverBorderColor: "rgba(220, 53, 69, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: onsiteData,
                },
                {
                    label: "Total Exhibition",
                    lineTension: 0.3,
                    backgroundColor: "rgba(23, 162, 184, 0.05)",
                    borderColor: "rgba(23, 162, 184, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(23, 162, 184, 1)",
                    pointBorderColor: "rgba(23, 162, 184, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(23, 162, 184, 1)",
                    pointHoverBorderColor: "rgba(23, 162, 184, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: exhibitionData,
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [
                    {
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }
                ],
                yAxes: [
                    {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return parseInt(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                ],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
            }
        }
    });

</script>
@endpush