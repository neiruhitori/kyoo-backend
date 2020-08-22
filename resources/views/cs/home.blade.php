@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Virtual Counter {{Auth::user()->Branch->name}}</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Active Appointment</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Queue No.</th>
                                    <th>Booking Code</th>
                                    <th>Booking Time</th>
                                    <th>Name</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>{{$appointment->number}}</td>
                                        <td>{{$appointment->booking_code}}</td>
                                        <td>{{$appointment->Slot->start_time}} - {{$appointment->Slot->end_time}}</td>
                                        <td>{{$appointment->name}}</td>
                                        <td>{{$appointment->Slot->Service->name}}</td>
                                        <td>
                                            @switch($appointment->status)
                                                @case('book')
                                                    <span class="badge badge-primary">Book</span>
                                                    @break
                                                @case('check in')
                                                    <span class="badge badge-success">Attend</span>
                                                    @break        
                                                @case('served')
                                                    <span class="badge badge-info">On Progress</span>
                                                    @break        
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($appointment->status)
                                                @case('book')
                                                    <div class="row">
                                                        <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="check in">
                                                            <button class="btn btn-success mx-2" data-toggle="tooltip" data-placement="bottom" title="Check In">
                                                                Check In
                                                            </button>
                                                        </form>
                                                        <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="no show">
                                                            <button class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="No Show">
                                                                No Show
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @break
                                                @case('check in')
                                                    <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="served">
                                                            <button class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Served">
                                                                Served
                                                            </button>
                                                        </form>
                                                    @break        
                                                @case('served')
                                                    <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="end served">
                                                            <button class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Served">
                                                                End Served
                                                            </button>
                                                        </form>
                                                    @break        
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>Queue No.</th>
                                <th>Booking Code</th>
                                <th>Booking Time</th>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Served Time</th>
                                <th>Status</th>
                            </tr>
                            @foreach ($historyAppointments as $index => $appointment)
                                <tr>
                                    <td>{{$appointment->number}}</td>
                                    <td>{{$appointment->booking_code}}</td>
                                    <td>{{$appointment->Slot->start_time}} - {{$appointment->Slot->end_time}}</td>
                                    <td>{{$appointment->name}}</td>
                                    <td>{{$appointment->Slot->Service->name}}</td>
                                    <td>
                                        @if ($appointment->status == 'served')
                                            {{$appointment->updated_at}}
                                            @else
                                            -
                                        @endif
                                    </td>
                                    <th>
                                        @switch($appointment->status)
                                            @case('end served')
                                                <span class="badge badge-success">End Served</span>
                                                @break
                                            @case('no show')
                                                <span class="badge badge-danger">No Show</span>
                                                @break        
                                        @endswitch
                                    </th>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable( {
                "ordering": false,
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush