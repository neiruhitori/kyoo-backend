<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ __('Kyoo Admin') }}</title>
    <link rel="icon" href="{{ asset('img/favico.png') }}" type="image/icon type">
    <!-- Custom fonts for this template-->
    <link href="{{asset('admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('admin/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <style>
        .img-logo {
            width: 200px;
            margin-bottom: 20px;
        }

        .img-playstore {
            width: 200px;
        }

        .bg-gradient-primary {
            background: linear-gradient(121.16deg, #189DCD 0.95%, #0A5194 97.59%);
        }

        .fullwidth {
            width: 100%;
        }

        @media(min-width: 300px) {
            p {
                margin-bottom: 0px;
            }

            .col-md-8 {
                margin-bottom: 16px;
            }

            .text-right {
                text-align: left !important;
            }
        }

        @media(min-width: 700px) {
            .text-right {
                text-align: right !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container pt-5">

        <!-- Outer Row -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <img src="{{ asset('img/logo.svg') }}" alt="" class="img-logo">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">{{ __('Appointment Status') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>{{ __('Transaction Detail') }}</h3>
                                        <hr>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Booking Status') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        @switch($appointment->status)
                                        @case('book')
                                        <span class="badge badge-primary py-2 px-4" style="font-size: 16px;">
                                            <b>{{ __('Book') }}</b>
                                        </span>
                                        @break
                                        @case('check in')
                                        <span class="badge badge-info py-2 px-4" style="font-size: 16px;">
                                            <b>{{ __('Check In') }}</b>
                                        </span>
                                        @break
                                        @case('no show')
                                        <span class="badge badge-danger py-2 px-4" style="font-size: 16px;">
                                            <b>{{ __('No Show') }}</b>
                                        </span>
                                        @break
                                        @case('served')
                                        <span class="badge badge-success py-2 px-4" style="font-size: 16px;">
                                            <b>{{ __('Served') }}</b>
                                        </span>
                                        @break
                                        @case('end served')
                                        <span class="badge badge-success py-2 px-4" style="font-size: 16px;">
                                            <b>{{ __('End Served') }}</b>
                                        </span>
                                        @break
                                        @endswitch
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('name.module', ['module' => __('Branch')]) }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->Slot->Service->Branch->name }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Date') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->date }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Service') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->Slot->Service->name }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Time Slot') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->Slot->start_time }} - {{ $appointment->Slot->end_time }}</b>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <h3>{{ __('Appointment Detail') }}</h3>
                                        <hr>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Queue No') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b class="text-primary" style="font-size: 26px">{{ $appointment->number }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Booking Code') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->booking_code }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Total Waiting Queue') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $total_waiting }}</b>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Currently Attending') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $currently_attending }}</b>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <h3>{{ __('User Detail') }}</h3>
                                        <hr>
                                    </div>
                                    <div class="col-md-4">
                                        <p>{{ __('Name') }}</p>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <b>{{ $appointment->name }}</b>
                                    </div>
                                    <div class="col-md-12 text-center mt-4 mb-3">
                                        <a href="" class="btn btn-primary">{{ __('Refresh') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="https://play.google.com/store/apps/details?id=com.kyoo.kyoo_app"
                                    target="_blank">
                                    <img src="{{ asset('img/playstore.png') }}" alt="" srcset="" class="img-playstore">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('admin/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('admin/js/sb-admin-2.min.js')}}"></script>
    @stack('name')
</body>

</html>