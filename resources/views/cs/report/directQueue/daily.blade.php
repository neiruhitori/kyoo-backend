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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Report</h6>
                </div>
                <div class="card-body">
                    @if (!$success)
                        @include('layouts.alert')
                    @endif
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                            <form action="" method="get">
                                <div class="form-group">
                                    <label for="">Select Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ $date }}" />
                                </div>
                                <div class="form-group">
                                    <label for="">Select Service</label>
                                    <select name="workstation_service_id" id="workstation_service_id" class="form-control">
                                        <option value="">All</option>
                                        @foreach ($workstationServices as $workstationService)
                                            <option value="{{ $workstationService->id }}">{{ $workstationService->Service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary mt-3">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>Queue Number</th>
                                        <th>Start Queue</th>
                                        <th>Served</th>
                                        <th>End Served</th>
                                        <th>Workstation</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($directQueues as $directQueue)
                                            <tr>
                                                <td>{{ $directQueue->queue_no }}</td>
                                                <td>{{ date('Y M d H:i:s', strtotime($directQueue->created_at)) }}</td>
                                                <td>
                                                    @if ($directQueue->called_at)
                                                        {{ date('Y M d H:i:s', strtotime($directQueue->called_at)) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($directQueue->done_at)
                                                        {{ date('Y M d H:i:s', strtotime($directQueue->done_at)) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $directQueue->WorkstationService->Workstation->name }}</td>
                                                <td>{{ $directQueue->WorkstationService->Service->name }}</td>
                                                <td>{{ $directQueue->status }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No data</td>
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
@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            const workstation_service_idOldValue = '{{ $workstation_service_id }}';
                
            $('#workstation_service_id').val(workstation_service_idOldValue);
        });
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: "Appointments {{ Auth::user()->Branch->name }} {{ $date ? '('.$date.')' : '' }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Appointments {{ Auth::user()->Branch->name }} {{ $date ? '('.$date.')' : '' }}"
                },
                {
                    extend: 'print',
                    title: "Appointments {{ Auth::user()->Branch->name }} {{ $date ? '('.$date.')' : '' }}"
                }
            ]
        })
    </script>
@endpush