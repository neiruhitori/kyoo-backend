@extends('layouts.app')

@section('isShowExpired')
@if($isShowExpiredBanner)
    <div class="alert alert-warning alert-block mb-4" style="display: flex; justify-content: center; gap: 2rem; align-items: center; background: linear-gradient(to right,  #062034, #13609A); color: white; border-radius: 0%;" >
        <span><i class="fas fa-clock mr-2"></i>{{ __('Your trial period will end in') }} <strong class="text-warning">{{$licenseExpirationDay}}</strong> {{ __('days') }}. {{ __('Upgrade your account to continue enjoying') }} <strong class="text-warning">KYOO</strong></span>
        {{-- <a href="mailto:support@awandigital.id" class="btn btn-warning"><strong>UPGRADE</strong></a> --}}
        <a href="{{ route('admin-branch.subscription') }}" class="btn btn-light px-4" style="color: #103C7C"><strong>UPGRADE</strong></a>
    </div>
@endif
@endsection


@section('content')
@if(session('show_login_popup'))
<script>
    $(document).ready(function() {
        $('#loginPopupModal').modal('show');
    });
</script>
@endif
<!-- Modal -->
<div class="modal fade" id="loginPopupModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center m-5 d-flex flex-column justify-content-center">
        <img src="{{ asset('img/trial-clock.png') }}" alt="" class="mx-auto d-block mb-4" style="80px">
        <h4 class="font-weight-bold text-dark">Waktu Trial tersisa {{ $licenseExpirationDay }} hari lagi</h4>
        <p class="mb-0 text-dark">Trial Akun Anda akan berakhir dalam <b style="color: black">{{ $licenseExpirationDay }}</b> hari.</p>
        <p class="text-dark">Upgrade akun anda agar dapat kembali menikmati layanan <b  style="color: black">KYOO</b></p>
        <div class="d-flex justify-content-center" style="gap:1rem;">
            <button type="button" class="btn btn-light px-4" data-dismiss="modal">Close</button>
            <a href="{{ route('admin-branch.subscription') }}" class="btn btn-primary px-4" style="background-color: #103C7C">UPGRADE</a>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">{{ __('Dashboard') }} {{ Auth::user()->Branch->name }}</h1>
</div>

<div class="row">
    <div class="col-md-12">
        @if(!Auth::user()->is_password_changed)
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>
                    {{ __('Your password not changed from registered') }}, <a href="{{ route('admin-branch.profile') }}">
                        {{ __('click here to change.') }}
                    </a>
                </strong>
            </div>
        @endif
    </div>
</div>

@if (Auth::user()->Branch->BranchType->is_appointment)
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #33A0FF4D; border-radius: 50%;">
                        <i class="fas fa-calendar fa-2x" style="color: #103C7C;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #103C7C;">
                                {{ number_format($totalAppointment) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                                {{ __('Total Appointment') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #C1F0DF; border-radius: 50%;">
                        <i class="fas fa-handshake fa-2x" style="color: #1CC88A;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #1CC88A;">
                                {{ number_format($totalServed) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                                {{ __('Served') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #FCC0B6; border-radius: 50%;">
                        <i class="fas fa-user-times fa-2x" style="color: #BF1D08;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #BF1D08;">
                                {{ number_format($totalNoShow) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                               {{ __('No Show') }}
                            </div>
                        </div>
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
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Total Appointment') }}</h6>
            </div>

            <div class="card-body">
                {{-- <div class="row">
                    <div class="col text-right">
                        <a href="{{ route('admin-branch.export.appointment') }}" class="btn btn-primary">
                            {{ __('Download Report') }}
                        </a>
                    </div>
                </div> --}}

                <div class="d-flex align-items-center justify-content-end" style="gap: 0.5rem;">
                    <div class="border rounded d-flex align-items-center px-3 py-2">
                        <div class="mr-2" style="width: 20px; height: 20px; background-color: #C1F0DF; border-radius: 50%;">
                        </div>
                        Layani
                    </div>

                    <div class="border rounded d-flex align-items-center px-3 py-2">
                        <div class="mr-2" style="width: 20px; height: 20px; background-color: #FCC0B6; border-radius: 50%;">
                        </div>
                        Tidak Hadir
                    </div>

                    <div>
                        <div class="dropdown border rounded">
                            <a class="custom-btn px-3 py-2 dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                               {{ $month }}
                            </a>

                            <div class="dropdown-menu">
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="1">January</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="2">February</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="3">March</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="4">April</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="5">May</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="6">June</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="7">July</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="8">August</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="9">September</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="10">October</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="11">November</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="12">December</a>
                            </div>
                        </div>
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
                        height="640" class="chartjs-render-monitor">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
{{-- END APPOINTMENT --}}

@if (Auth::user()->Branch->BranchType->is_exhibition)
    @include('adminBranch.exhibitionDashboard')
@endif

{{-- START DIRECT QUEUE --}}
@if (Auth::user()->Branch->BranchType->is_direct_queue)
<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #33A0FF4D; border-radius: 50%;">
                        <i class="fas fa-calendar fa-2x" style="color: #103C7C;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #103C7C;">
                                {{ number_format($totalDirectQueue) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                                {{ __('Total Visit') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #C1F0DF; border-radius: 50%;">
                        <i class="fas fa-handshake fa-2x" style="color: #1CC88A;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #1CC88A;">
                                {{ number_format($totalDirectQueueServed) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                                {{ __('Served') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col d-flex">
                        <div class="d-flex justify-content-center align-items-center mr-5 border" 
                        style="width: 60px; height: 60px; background-color: #FCC0B6; border-radius: 50%;">
                        <i class="fas fa-user-times fa-2x" style="color: #BF1D08;"></i>
                    </div>

                        <div class="align-content-center">
                            <div class="h4 mb-1 font-weight-bold" style="color: #BF1D08;">
                                {{ number_format($totalDirectQueueNoShow) }}
                            </div>
                            <div class="font-weight-bold text-gray text-uppercase mb-1">
                               {{ __('No Show') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Served') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalDirectQueueServed) }}
                        </div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('No Show') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalDirectQueueNoShow) }}
                        </div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

<div class="row">
    <div class="col-xl-12 col-lg-7">
        <!-- Area Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Total Visit') }}</h6>
            </div>

            <div class="card-body">

                <div class="d-flex align-items-center justify-content-end" style="gap: 0.5rem;">
                    <div class="border rounded d-flex align-items-center px-3 py-2">
                        <div class="mr-2" style="width: 20px; height: 20px; background-color: #C1F0DF; border-radius: 50%;">
                        </div>
                        Layani
                    </div>

                    <div class="border rounded d-flex align-items-center px-3 py-2">
                        <div class="mr-2" style="width: 20px; height: 20px; background-color: #FCC0B6; border-radius: 50%;">
                        </div>
                        Tidak Hadir
                    </div>

                    <div>
                        <div class="dropdown border rounded">
                            <a class="custom-btn px-3 py-2 dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                               {{ $month }}
                            </a>

                            <div class="dropdown-menu">
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="1">January</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="2">February</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="3">March</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="4">April</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="5">May</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="6">June</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="7">July</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="8">August</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="9">September</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="10">October</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="11">November</a>
                                <div class="dropdown-divider"></div>
                                <a class="h6 font-weight-bold py-1 dropdown-item" href="#" data-value="12">December</a>
                            </div>
                        </div>
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

                    <canvas id="myAreaChartDirectQueue" style="display: block; height: 320px; width: 387px;" width="774"
                        height="640" class="chartjs-render-monitor">
                    </canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
{{-- END DIRECT QUEUE --}}
@endsection

<style>
.custom-btn{
    color: #858796;
    text-decoration: none;
    display: inline-block;
    font-weight: 400;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.35rem;
    user-select: none;
}
.custom-btn:hover{
    color: #858796;
    text-decoration: none;
}
.dropdown-menu {
  max-height: 200px;
  overflow-y: auto;
}
.custom-btn:focus{
    color: #ffff;
    background-color: #103C7C;
    text-decoration: none;
}

#monthSelect:focus {
  background-color: #103C7C;
  color: white;
  border-color: #103C7C;
}
#monthSelect option {
  color: #333;
  background-color: white;
  border-bottom: 1px solid #ddd;
   padding: 5px;
}
</style>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>


@if (Auth::user()->Branch->BranchType->is_appointment)
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
            // Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            // Chart.defaults.global.defaultFontColor = '#858796';

            function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
            }
            const appointments = JSON.parse('{!! $appointmentGraph !!}')
            let labels = []
            let data = []
            let servedData = []
            let noShowData = []
            

            appointments.forEach(appointment => {
                labels.push(appointment.day)
                data.push(appointment.total)
                servedData.push(appointment.served)
                noShowData.push(appointment.no_show)
            });
            // Area Chart Example
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                            {
                                label: "Served",
                                data: servedData,
                                backgroundColor: "#C1F0DF",
                                stack: 'queue',
                                borderRadius: 15,
                                borderSkipped: 'bottom',
                            },
                            {
                                label: "No Show",
                                data: noShowData,
                                backgroundColor: "#FCC0B6",
                                stack: 'queue',
                                borderRadius: 15,
                                borderSkipped: 'bottom',
                            }
                    ]
                },
                options: {
                    plugins: {
                       datalabels: {
                                display: function (context) {
                                            return context.datasetIndex === 1;
                                        },
                                formatter: function (value, context) {
                                              const total = data[context.dataIndex]; // ambil total kunjungan
                                                return total > 0 ? total : ''; 
                                    },
                                color: '#000',
                                anchor: 'end',
                                align: 'end', 
                                offset: 6,
                                clamp: true,
                                clip: false,
                                font: {
                                    size: 12
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                              legend: {
                                display: false
                            },
                        },
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 45,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        },
                        xAxes: [{
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
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
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
                    }],
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

            async function fetchData(month) {
                const response = await axios.get('getDataChart', {
                        params: { month: month }
                    })
                const appointments = response.data.data;
                let labels = [];
                let data = [];
                let servedData = [];
                let noShowData = [];

                appointments.forEach(item => {
                    labels.push(item.day)
                    data.push(item.total)
                    servedData.push(item.served)
                    noShowData.push(item.no_show)
                });

                myLineChart.data.labels = labels;
                myLineChart.data.datasets[0].data = servedData;
                myLineChart.data.datasets[1].data = noShowData;
                myLineChart.update();
            }

    document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
            e.preventDefault();
                const month = this.dataset.value;
                const label = this.innerText;

                // Update dropdown label
                const toggle = this.closest('.dropdown').querySelector('.dropdown-toggle');
                toggle.innerText = label;

                // Fetch and update chart
                fetchData(month);
            });
    });

</script>
@endif

@if (Auth::user()->Branch->BranchType->is_direct_queue)
<script>
    const directQueues = JSON.parse('{!! $directQueueGraph !!}')
            let directQueueLabels = []
            let directQueueData = []
            let servedData = []
            let noShowData = []

            directQueues.forEach(directQueue => {
                directQueueLabels.push(directQueue.day)
                directQueueData.push(directQueue.total)
                servedData.push(directQueue.served)
                noShowData.push(directQueue.no_show)
            });
            // Area Chart Example
            var ctxDirectQueue = document.getElementById("myAreaChartDirectQueue");
            Chart.register(ChartDataLabels);
            var myLineChartDirectQueue = new Chart(ctxDirectQueue, {
                type: 'bar',
                data: {
                    labels: directQueueLabels,
                    datasets: [
                            {
                                label: "Served",
                                data: servedData,
                                backgroundColor: "#C1F0DF",
                                stack: 'queue',
                                borderRadius: 15,
                                borderSkipped: 'bottom',
                            },
                            {
                                label: "No Show",
                                data: noShowData,
                                backgroundColor: "#FCC0B6",
                                stack: 'queue',
                                borderRadius: 15,
                                borderSkipped: 'bottom',
                            }
                    ]
                },
                options: {
                    plugins: {
                       datalabels: {
                                display: function (context) {
                                            return context.datasetIndex === 1;
                                        },
                                formatter: function (value, context) {
                                              const total = directQueueData[context.dataIndex]; // ambil total kunjungan
                                                return total > 0 ? total : ''; 
                                    },
                                color: '#000',
                                anchor: 'end',
                                align: 'end', 
                                offset: 6,
                                clamp: true,
                                clip: false,
                                font: {
                                    size: 12
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                              legend: {
                                display: false
                            },
                        },
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 45,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        },
                        xAxes: [{
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
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
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
                    }],
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

            async function fetchData(month) {
                const response = await axios.get('getDataChart', {
                        params: { month: month }
                    })
                const data = response.data.data;
                directQueueLabels = [];
                directQueueData = [];
                servedData = [];
                noShowData = [];

                data.forEach(item => {
                        directQueueLabels.push(item.day)
                        directQueueData.push(item.total)
                        servedData.push(item.served)
                        noShowData.push(item.no_show)
                    });

                myLineChartDirectQueue.data.labels = directQueueLabels;
                myLineChartDirectQueue.data.datasets[0].data = servedData;
                myLineChartDirectQueue.data.datasets[1].data = noShowData;
                myLineChartDirectQueue.update();
            }
            
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function (e) {
        e.preventDefault();
            const month = this.dataset.value;
            const label = this.innerText;

            // Update dropdown label
            const toggle = this.closest('.dropdown').querySelector('.dropdown-toggle');
            toggle.innerText = label;

            // Fetch and update chart
            fetchData(month);
        });
  });
</script>
@endif

@endpush