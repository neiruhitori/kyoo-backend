@extends('layouts.app')
@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.3/css/buttons.dataTables.min.css">
    <style>
        .buttons-excel {
            background-color: #48bb78 !important;
            color: white !important;
            border: 0px !important;
            font-weight: 500px !important;
        }
        .buttons-pdf {
            background-color: #e53e3e !important;
            color: white !important;
            border: 0px !important;
            font-weight: 500px !important;
        }
        .buttons-print {
            background-color: #cbd5e0 !important;
            color: #333333 !important;
            border: 0px !important;
            font-weight: 500px !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            @if (!$success)
                @include('layouts.alert')
            @endif
            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3"> --}}
                    {{-- </div> --}}
                    <div class="">
                        <div class="row p-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <h5 class="m-0 font-weight-bold" style="color: #103C7C">{{ __('Upcoming Appointment') }}</h5>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <form action="" method="get" class="d-flex align-items-center">
                                <div class="form-row align-items-end">
                                    <div class="col-auto">
                                        <label for="">{{ __('Select Reservation Date') }}</label>
                                        <input type="date" name="date" class="form-control" value="{{ $date }}" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"/>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary mt-3 px-4" style="background-color: #103C7C">{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                        <th>{{ __('Order Date') }}</th>
                                        <th>{{ __('Reservation Date') }}</th>
                                        <th>{{ __('Booking Code') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile Phone') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Slot') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($appointment_onsites as $appointment_onsite)
                                            <tr>
                                                <td>{{ $appointment_onsite->created_at->formatLocalized('%d-%b-%Y') }}</td>
                                                <td>{{ date('d-M-Y', strtotime($appointment_onsite->date)) }}</td>
                                                <td>{{ strtoupper($appointment_onsite->booking_code) }}</td>
                                                <td>{{ $appointment_onsite->name }}</td>
                                                <td>{{ $appointment_onsite->email ?? '-' }}</td>
                                                <td>{{ $appointment_onsite->phone }}</td>
                                                <td>{{ $appointment_onsite->Service->name }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($appointment_onsite->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment_onsite->end_time)->format('H:i') }}
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{route('admin-branch.appointment-onsites.slot.edit', $appointment_onsite->id)}}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{
                                                            __('edit.module', ['module' => __('Appointment')])
                                                        }}"
                                                    >
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">{{ __('No data') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
