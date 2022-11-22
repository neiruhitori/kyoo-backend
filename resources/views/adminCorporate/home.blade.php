@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h3 class="mb-0">Informasi Geografis</h3>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kunjungan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVisit }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Dilayani
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalServed }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tidak Hadir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNoShow }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div id="app">
            <branch-monitoring-map-component
                :branches="{{ $branches }}"
            />
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endpush

@push('js')
<script src="{{ mix('js/app.js') }}"></script>
@endpush