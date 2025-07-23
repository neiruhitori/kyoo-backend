@extends('layouts.app')
@push('css')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
    table {
            border: 1px solid #33A0FF4D; 
        }

    table th,
    table td {
            border: 1px solid #33A0FF4D !important;
            }
    table td {
                color: black
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
                <div class="card-body">
                    <h5 class="mb-4 font-weight-bold" style="color: #103c7c">{{ __('Daily Report') }}</h5>

                    <form action="" method="get">
                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ __('Select Date') }}</label>
                                    <input type="date" name="date" class="form-control" value="{{ $date }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{ __('Select Service') }}</label>
                                        <select name="service_id" id="service_id" class="form-control">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach (Auth::user()->Branch->Service as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-primary mt-3" style="background-color: #103c7c">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                        <th>{{ __('Queue Number') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Appointment Date') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Slot') }}</th>
                                        <th>{{ __('Channel') }}</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->number }}</td>
                                                <td>{{ $appointment->name }}</td>
                                                <td>{{ $appointment->date }}</td>
                                                <td>{{ $appointment->Slot->Service->name }}</td>
                                                <td>{{ $appointment->Slot->start_time }} -
                                                    {{ $appointment->Slot->end_time }}</td>
                                                <td>{{ $appointment->appointment_channel }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">{{ __('No data') }}</td>
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
    <script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.3/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            const service_idOldValue = '{{ $service_id }}';

            $('#service_id').val(service_idOldValue);
        });
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
            "language": {
               "emptyTable": "{{ __('No Data') }}",
                "info": "{{ __('Displaying :start to :end of :total data') }}"
                            .replace(':start', '_START_')
                            .replace(':end', '_END_')
                            .replace(':total', '_TOTAL_'),
                "infoEmpty": "{{ __('No Data') }}",
                "infoFiltered": "{{ __('(filtered from :max total data)') }}".replace(':max','_MAX_'),
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "{{ __('Show :menu data') }}".replace(':menu', '_MENU_'),
                "loadingRecords": "{{ __('Loading...') }}",
                "processing": "{{ __('Processing...') }}",
                "search": "{{ __('Search:') }}",
                "zeroRecords": "{{ __('No data found') }}",
                "paginate": {
                    "first": "{{ __('First') }}",
                        "last": "{{ __('Last') }}",
                        "next": "{{ __('Next') }}",
                        "previous": "{{ __('Previous') }}"
                },
                "aria": {
                    "sortAscending": "{{ __(': enable to sort the column in ascending order') }}",
                    "sortDescending": "{{ __(': enable to sort the column in descending order') }}"
                }
            },
            "buttons": [{
                    extend: 'excelHtml5',
                    title: "{{ __('Appointment Queue') }} {{ Auth::user()->Branch->name }} {{ count($appointments) > 0 ? '(' . $appointments[0]->date . ')' : '' }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "{{ __('Appointment Queue') }} {{ Auth::user()->Branch->name }} {{ count($appointments) > 0 ? '(' . $appointments[0]->date . ')' : '' }}"
                },
                {
                    extend: 'print',
                    text: "{{ __('Print') }}",
                    title: "{{ __('Appointment Queue') }} {{ Auth::user()->Branch->name }} {{ count($appointments) > 0 ? '(' . $appointments[0]->date . ')' : '' }}"
                }
            ]
        })
    </script>
@endpush
