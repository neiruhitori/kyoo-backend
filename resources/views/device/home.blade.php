@extends('layouts.app')

@push('css')
    <style>
        .wrapper-menus {
            display: flex;
            justify-content: space-evenly;
            margin: 100px 0;
        }

        .wrapper-button {
            text-align: center;
            margin: 45px 0;
        }

        .button-style {
            padding: 10px;
            border: 2px solid #1885cd;
            border-radius: 5px;
            background-color: #189DCD;
            color: #FFFF;
        }
    </style>
@endpush

@php
    // $tv_token = auth()->user()->Branch->TVconfiguration->TVToken;
    // $tv_token = $tv_token ? $tv_token->token : Str::random(12);

    // $webkiosk_token = auth()->user()->Branch->WebkioskConfiguration->WebkioskToken;
    // $webkiosk_token = $webkiosk_token ? $webkiosk_token->token : Str::random(12);
@endphp

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('Device Dashboard') }}</h1>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.alert')
    </div>

    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row wrapper-menus">
                    @if (auth()->user()->Branch->TVconfiguration && auth()->user()->Branch->TVconfiguration->TVToken)
                    <div class="col-md-6 col-12 wrapper-button align-self-start">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('img/device/signage.jpeg') }}" style="width: 12rem" alt="">
                            <a href="{{route('device.web-monitor', ['branch_id' => auth()->user()->branch_id, 'token' => $tv_token])}}" class="button-style mt-4">Monitor Antrian (TV)</a>
                        </div>
                    </div>
                    @else
                    <div class="col-md-6 col-12 wrapper-button align-self-start">
                        <div class="d-flex flex-column align-items-center mb-3">
                            <img src="{{ asset('img/device/signage.jpeg') }}" style="width: 12rem" alt="">
                            <button type="button" disabled class="btn btn-secondary py-3 px-4 mt-4">Monitor Antrian (TV)</button>
                        </div>
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0 p-3">Konfigurasi Monitor Antrian TV tidak lengkap, mohon melakukan konfigurasi dari admin cabang</p>
                        </div>
                    </div>
                    @endif


                    @if (auth()->user()->Branch->WebkioskConfiguration && auth()->user()->Branch->WebkioskConfiguration->WebkioskToken)
                    <div class="col-md-6 col-12 wrapper-button align-self-start">
                        <div class="d-flex flex-column align-items-center">
                            <img src="{{ asset('img/device/kiosk.jpeg') }}" style="width: 9rem" alt="">
                            <a href="{{route('device.web-kiosk-ui', ['branch_id' => auth()->user()->branch_id, 'token' => $webkiosk_token])}}" class="button-style mt-3">Web Kiosk UI</a>
                        </div>
                    </div>
                    @else
                    <div class="col-md-6 col-12 wrapper-button align-self-start">
                        <div class="d-flex flex-column align-items-center mb-3">
                            <img src="{{ asset('img/device/kiosk.jpeg') }}" style="width: 9rem" alt="">
                            <button type="button" disabled class="btn btn-secondary py-3 px-4 mt-3">Web Kiosk UI</button>
                        </div>
                        <div class="alert alert-danger" role="alert">
                            <p class="mb-0 p-3">Konfigurasi Web Kiosk tidak lengkap, mohon melakukan konfigurasi dari admin cabang</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
