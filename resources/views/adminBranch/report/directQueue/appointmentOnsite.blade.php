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
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Appointment Report') }}</h6>
                </div>
                <div class="card-body">
                    @if (!$success)
                        @include('layouts.alert')
                    @endif
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                            <form action="" method="get">
                                <div class="form-group d-none" id="reservation_date">
                                    <label for="">{{ __('Select Reservation Date') }}</label>
                                    <input type="date" name="reservation_date" class="form-control" value="{{ $reserve_date ?? $start_date }}" />
                                </div>
                                <div class="form-group" id="start_date">
                                    <label for="">{{ __('Select Start Date') }}</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $start_date }}" />
                                </div>
                                <div class="form-group" id="end_date">
                                    <label for="">{{ __('Select End Date') }}</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}" />
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary mt-3">{{ __('Filter') }}</button>
                                </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Sort') }}</label>
                                    <select name="sort" id="sort" class="form-control">
                                        <option value="order_date">Order Date</option>
                                        <option value="reserve_date" {{ isset($reserve_date) && $reserve_date ? 'selected' : '' }}>Reservation Date</option>
                                    </select>
                                </div>
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
                            <div class="col-lg-4 col-md-12">
                                
                                <div class="form-group">
                                    <label for="">{{ __('Booking Form') }}</label>
                                    <select name="booking_form" id="booking_form" class="form-control">
                                        <option value="standard-form" {{ $booking_form == 'standard-form' ? 'selected' : '' }}>Standard Form</option>
                                        <option value="form-medical-1" {{ $booking_form == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                        <option value="form-financing" {{ $booking_form == 'form-financing' ? 'selected' : '' }}>Form Financing</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                @if ($booking_form == 'standard-form')
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <th>{{ __('Order Date') }}</th>
                                            <th>{{ __('Reservation Date') }}</th>
                                            <th>{{ __('Kode Unik') }}</th>
                                            <th>{{ __('Queue No') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Mobile Phone') }}</th>
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Slot') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </thead>
                                        <tbody>
                                            @forelse ($appointment_onsites as $appointment_onsite)
                                                <tr>
                                                    <td>{{ $appointment_onsite->created_at->formatLocalized('%d-%b-%Y') }}</td>
                                                    <td>{{ date('d-M-Y', strtotime($appointment_onsite->date)) }}</td>
                                                    <td>{{ strtoupper($appointment_onsite->booking_code) }}</td>
                                                    <td>{{ $appointment_onsite->DirectQueue->queue_no ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->name }}</td>
                                                    <td>{{ $appointment_onsite->email ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->phone }}</td>
                                                    <td>{{ $appointment_onsite->Service->name }}</td>
                                                    <td>{{ $appointment_onsite->Slot->start_time }} - {{ $appointment_onsite->Slot->end_time }}</td>
                                                    <td>{{ $appointment_onsite->is_used ? 'Check-In' : 'Belum Check-In' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">{{ __('No data') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                @elseif ($booking_form == 'form-financing')
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>{{ __('Order Date') }}</th>
                                        <th>{{ __('Reservation Date') }}</th>
                                        <th>{{ __('Kode Unik') }}</th>
                                        <th>{{ __('Queue No') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile Phone') }}</th>
                                        <th>{{ __('No. Kontrak') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Slot') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($appointment_onsites as $appointment_onsite)
                                            <tr>
                                                <td>{{ $appointment_onsite->created_at->formatLocalized('%d-%b-%Y') }}</td>
                                                <td>{{ date('d-M-Y', strtotime($appointment_onsite->date)) }}</td>
                                                <td>{{ strtoupper($appointment_onsite->booking_code) }}</td>
                                                <td>{{ $appointment_onsite->DirectQueue->queue_no ?? '-' }}</td>
                                                <td>{{ $appointment_onsite->name }}</td>
                                                <td>{{ $appointment_onsite->email }}</td>
                                                <td>{{ $appointment_onsite->phone }}</td>
                                                <td>{{ $appointment_onsite->contract_number }}</td>
                                                <td>{{ $appointment_onsite->Service->name }}</td>
                                                <td>{{ $appointment_onsite->Slot->start_time }} - {{ $appointment_onsite->Slot->end_time }}</td>
                                                <td>{{ $appointment_onsite->is_used ? 'Check-In' : 'Belum Check-In' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">{{ __('No data') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @elseif ($booking_form == 'form-medical-1')
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <th>{{ __('Order Date') }}</th>
                                            <th>{{ __('Reservation Date') }}</th>
                                            <th>{{ __('Booking Code') }}</th>
                                            <th>{{ __('Queue No') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Date of Birth') }}</th>
                                            <th>{{ __('Address') }}</th>
                                            <th>{{ __('Mobile Phone') }}</th>
                                            <th>{{ __('Emergency Number') }}</th>
                                            <th>{{ __('NIK/Passport Number') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Reason for Visit') }}</th>
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Slot') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </thead>
                                        <tbody>
                                            @forelse ($appointment_onsites as $appointment_onsite)
                                                <tr>
                                                    <td>{{ $appointment_onsite->created_at->formatLocalized('%d-%b-%Y') }}</td>
                                                    <td>{{ date('d-M-Y', strtotime($appointment_onsite->date)) }}</td>
                                                    <td>{{ strtoupper($appointment_onsite->booking_code) }}</td>
                                                    <td>{{ $appointment_onsite->DirectQueue->queue_no ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->name }}</td>
                                                    <td>
                                                        {{ $appointment_onsite->date_of_birth ? date('d-M-Y', strtotime($appointment_onsite->date_of_birth)) : '-' }}
                                                    </td>
                                                    <td>{{ $appointment_onsite->address ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->phone }}</td>
                                                    <td>{{ $appointment_onsite->emergency_number ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->passport_number ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->email }}</td>
                                                    <td>{{ $appointment_onsite->reason_for_visit ?? '-' }}</td>
                                                    <td>{{ $appointment_onsite->Service->name }}</td>
                                                    <td>{{ $appointment_onsite->Slot->start_time }} - {{ $appointment_onsite->Slot->end_time }}</td>
                                                    <td>{{ $appointment_onsite->is_used ? 'Check-In' : 'Belum Check-In' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">{{ __('No data') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {    
    const sortOpt = document.getElementById('sort');
    
    function updateDisplay() {
        if (sortOpt.value == 'reserve_date') {
            document.getElementById('reservation_date').classList.remove('d-none');
            document.getElementById('start_date').classList.add('d-none');
            document.getElementById('end_date').classList.add('d-none');
        } else {
            document.getElementById('reservation_date').classList.add('d-none');
            document.getElementById('start_date').classList.remove('d-none');
            document.getElementById('end_date').classList.remove('d-none');
        }
    }

    updateDisplay();

    sortOpt.addEventListener('change', updateDisplay);
});

    </script>
@endsection
@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
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
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: "Antrian Appointment Booking {{ Auth::user()->Branch->name }} {{ $appointment_onsites->isNotEmpty() ? '('.$appointment_onsites->first()->date.')' : '' }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Antrian Appointment Booking {{ Auth::user()->Branch->name }} {{ $appointment_onsites->isNotEmpty() ? '('.$appointment_onsites->first()->date.')' : '' }}"
                },
                {
                    extend: 'print',
                    text: 'Cetak',
                    title: "Antrian Appointment Booking {{ Auth::user()->Branch->name }} {{ $appointment_onsites->isNotEmpty() ? '('.$appointment_onsites->first()->date.')' : '' }}"
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
