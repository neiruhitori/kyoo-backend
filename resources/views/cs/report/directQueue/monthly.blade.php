@extends('layouts.appCS')
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
    </style>
@endpush
@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    {{ __('Information') }}
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    {{ __('Show') }}
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                    {{ __('For free license, report only available for last 3 months') }}
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Monthly Report') }}</h6>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        @include('layouts.alert')
                    @endif

                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="">{{ __('Select Month') }}</label>
                                        <select name="month" class="form-control">
                                            @foreach ($months as $k => $m)
                                                <option value="{{ $k + 1 }}" {{ $month != $k + 1 ?: 'selected' }}>
                                                    {{ __($m) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="">{{ __('Select Year') }}</label>
                                        <select name="year" class="form-control">
                                            @for ($y = date('Y') - 20; $y <= date('Y'); $y++)
                                                <option value="{{ $y }}" {{ $year != $y ?: 'selected' }}>
                                                    {{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">{{ __('Select Service') }}</label>
                                    <select name="workstation_service_id" id="workstation_service_id" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach ($workstationServices as $workstationService)
                                            <option value="{{ $workstationService->id }}"
                                                {{ $workstationService->id != $workstation_service_id ?: 'selected' }}>
                                                {{ $workstationService->Service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-primary mt-3">{{ __('Filter') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>{{ __('Queue Number') }}</th>
                                        <th>{{ __('Booking Code') }}</th>
                                        <th>{{ __('Queue Taken') }}</th>
                                        <th>{{ __('Queue Called') }}</th>
                                        <th>{{ __('Start Service') }}</th>
                                        <th>{{ __('Finish Service') }}</th>
                                        <th>{{ __('Waiting Duration') }} </th>
                                        <th>{{ __('Service Duration (Call)') }} </th>
                                        <th>{{ __('Service Duration') }} </th>
                                        <th>{{ __('Workstation') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Sub Service') }}</th>
                                        <th>{{ __('Service Transfer') }}</th>
                                        <th>{{ __('Staff') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Requeue') }}</th>
                                    </thead>
                                    <tbody>
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
            const workstation_service_idOldValue = '{{ $workstation_service_id }}';

            $('#workstation_service_id').val(workstation_service_idOldValue);
        });
        $('#dataTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('cs.report.directQueue.getMonthly') }}',
                type: 'GET',
                data: function(d) {
                    d.month = '{{ $month }}';
                    d.year = '{{ $year }}';
                    d.workstation_service_id = '{{ $workstation_service_id }}';
                }
            },
            columns: [
                { data: 'queue_no' },
                { data: 'booking_code' },
                { data: 'created_at' },
                { data: 'call_time' },
                { data: 'called_at' },
                { data: 'done_at' },
                { data: 'waiting_time' },
                { data: 'service_time_called' },
                { data: 'service_time' },
                { data: 'workstation' },
                { data: 'service' },
                { data: 'sub_service' },
                { data: 'service_transfer' },
                { data: 'customer_service' },
                { data: 'status' },
                { data: 'requeue_count' },
            ],
            "ordering": false,
            "dom": 'Bfrtip',
            "buttons": [{
                    extend: 'excelHtml5',
                    title: "{{ __('Direct Queue') }} {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "{{ __('Direct Queue') }} {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                },
                {
                    extend: 'print',
                    text: "{{ __('Print') }}",
                    title: "{{ __('Direct Queue') }} {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                }
            ],
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
            }
        })
    </script>
@endpush
