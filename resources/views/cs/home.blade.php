@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">VCT Branch: {{Auth::user()->Branch->name}}</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Appointment</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>{{$appointment->id}}</td>
                                        <td>{{$appointment->Slot->Service->name}}</td>
                                        <td>
                                            @switch($appointment->status)
                                                @case('book')
                                                    <span class="badge badge-primary">Book</span>
                                                    @break
                                                @case('attend')
                                                    <span class="badge badge-success">Attend</span>
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
                                                            <input type="hidden" name="status" value="attend">
                                                            <button class="btn btn-success mx-2" data-toggle="tooltip" data-placement="bottom" title="Attend">
                                                                <i class="fas fa-fw fa-clipboard-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="not attend">
                                                            <button class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Not Show">
                                                                <i class="fas fa-fw fa-window-close"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @break
                                                @case('attend')
                                                    <form action="{{route('cs.appointment.update', $appointment->id)}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="served">
                                                            <button class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Not Show">
                                                                <i class="fas fa-fw fa-check-double"></i>
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
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">History Appointment</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>No.</th>
                                <th>Service</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                            @foreach ($historyAppointments as $index => $appointment)
                                <tr>
                                    <td>{{++$index}}</td>
                                    <td>{{$appointment->Slot->Service->name}}</td>
                                    <td>{{$appointment->Slot->start_time}}</td>
                                    <th>
                                        @switch($appointment->status)
                                            @case('served')
                                                <span class="badge badge-success">Served</span>
                                                @break
                                            @case('not attend')
                                                <span class="badge badge-danger">Not Show</span>
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
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush