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
    </style>
@endpush
@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    Informasi
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    Tampilkan
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                    {{ __('For free license, report only available for last 3 months') }}
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
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
                                        <th>{{ __('Start Queue') }}</th>
                                        <th>{{ __('Served') }}</th>
                                        <th>{{ __('End Served') }}</th>
                                        <th>{{ __('Service Time') }} ({{ __('Menit') }})</th>
                                        <th>{{ __('Workstation') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Service Transfer') }}</th>
                                        <th>{{ __('Status') }}</th>
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
                { data: 'created_at' },
                { data: 'called_at' },
                { data: 'done_at' },
                { data: 'service_time' },
                { data: 'workstation' },
                { data: 'service' },
                { data: 'service_transfer' },
                { data: 'status' }
            ],
            "ordering": false,
            "dom": 'Bfrtip',
            "buttons": [{
                    extend: 'excelHtml5',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                },
                {
                    extend: 'print',
                    text: 'Cetak',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ __($months[$month - 1]) }} {{ $year }}"
                }
            ],
            "language": {
                "emptyTable": "Tidak ada data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(ter-filter dari _MAX_ total data)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Tampilkan _MENU_ data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Berikutnya",
                    "previous": "Sebelum"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom menaik",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom menurun"
                }
            }
        })
    </script>
@endpush
