@extends('layouts.app')
@push('css')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.3/css/buttons.dataTables.min.css">
    
@endpush
@section('content')

    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Webhook List') }}</h6>
                </div>
                <div class="card-body">
                    {{-- @if (!$success)
                        @include('layouts.alert')
                    @endif --}}
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                                <form action="" method="get">
                                <div class="form-group">
                                    <label for="">{{ __('Select Start Date') }}</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="" />
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Select Service') }}</label>
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                      
                                    </select>
                                </div>
                            </form>
                            </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>{{ __('Appointment Date') }}</th>
                                        <th>{{ __('Queue Number') }}</th>
                                        <th>{{ __('Booking Code') }}</th>
                                        <th>{{ __('Customer Name') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($directQueues as $directQueue) --}}
                                            <tr>
                                                <td>Tanggal Antrian</td>
                                                <td>Nomor Antrian</td>
                                                <td>Kode Antrian</td>
                                                <td>Nama</td>
                                                <td>Layanan</td>
                                                <td>
                                                    <button class="btn btn-success">
                                                        Re-send
                                                    </button>
                                                </td>
                                            </tr>
                                        {{-- @empty --}}
                                            {{-- <tr>
                                                <td colspan="5" class="text-center">{{ __('No data') }}</td>
                                            </tr>
                                        @endforelse --}}
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
