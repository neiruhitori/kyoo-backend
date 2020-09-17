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
                    <h6 class="m-0 font-weight-bold text-primary">Mini Report</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                            <form action="" method="get">
                                <div class="form-group">
                                    <label for="">Select Date</label>
                                    <input type="date" name="date" class="form-control" />
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
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Appointment Date</th>
                                        <th>Service</th>
                                        <th>Slot</th>
                                        <th>Queue Number</th>
                                        <th>Appointment Channel</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->name }}</td>
                                                <td>{{ $appointment->phone }}</td>
                                                <td>{{ $appointment->date }}</td>
                                                <td>{{ $appointment->Slot->Service->name }}</td>
                                                <td>{{ $appointment->Slot->start_time }} - {{ $appointment->Slot->end_time }}</td>
                                                <td>{{ $appointment->number }}</td>
                                                <td>{{ $appointment->appointment_channel }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">No data</td>
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
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: "Appointment KYOO {{ $appointments[0]->date }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Appointment KYOO {{ $appointments[0]->date }}"
                },
                {
                    extend: 'print',
                    title: "Appointment KYOO {{ $appointments[0]->date }}"
                }
            ]
        })
    </script>
@endpush