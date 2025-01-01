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

    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Daily Report') }}</h6>
                </div>
                <div class="card-body">
                    @if (!$success)
                        @include('layouts.alert')
                    @endif
                    <div class="row">
                        <div class="col-lg-4 col-md-12">
                                <form action="" method="get">
                                <div class="form-group">
                                    <label for="">{{ __('Select Start Date') }}</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ $start_date }}" />
                                </div>
                                <div class="form-group">
                                    <label for="">{{ __('Select End Date') }}</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ $end_date }}" />
                                </div>
                                <div class="form-group">
                                    <input id="formatTime" type="checkbox" name="formatTime" value="inMinutes" {{ $time_format == 'inMinutes' ? 'checked' : '' }}>
                                    <label onclick="toggleCheckbox()" style="cursor: pointer;user-select: none;" class="mx-2" for="">Format durasi dalam satuan menit</label>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary mt-3">{{ __('Filter') }}</button>
                                </div>

                            </div>

                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="">{{ __('Select Service') }}</label>
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="">{{ __('All') }}</option>
                                        @foreach ($workstationServices as $workstationService)
                                            <option value="{{ $workstationService->service_id }}">
                                                {{ $workstationService->Service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Pilih Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="all">{{ __('All') }}</option>
                                        <option value="waiting"  {{ $status_sort == 'waiting' ? 'selected': '' }}>Menunggu</option>
                                        <option value="served" {{ $status_sort == 'served' ? 'selected': '' }}>Dilayani</option>
                                        <option value="end served" {{ $status_sort == 'end served' ? 'selected': '' }}>Selesai Dilayani</option>
                                        <option value="no show" {{ $status_sort == 'no show' ? 'selected': '' }}>Tidak Hadir</option>
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
                                        <th>{{ __('Queue Number') }}</th>
                                        <th>{{ __('Kode Unik') }}</th>
                                        <th>{{ __('Ambil Antrian') }}</th>
                                        <th>{{ __('Antrian Dipanggil') }}</th>
                                        <th>{{ __('Mulai Layanan') }}</th>
                                        <th>{{ __('Selesai Layanan ') }}</th>
                                        <th>{{ __('Durasi Tunggu') }} </th>
                                        <th>{{ __('Durasi Layanan (Panggil)') }} </th>
                                        <th>{{ __('Durasi Layanan') }} </th>
                                        <th>{{ __('Workstation') }}</th>
                                        <th>{{ __('Service') }}</th>
                                        <th>{{ __('Sub Layanan') }}</th>
                                        <th>{{ __('Service Transfer') }}</th>
                                        <th>{{ __('Petugas Layanan') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($directQueues as $directQueue)
                                            <tr>
                                                <td>{{ $directQueue->queue_no }}</td>
                                                <td>{{ $directQueue->booking_code }}</td>
                                                <td>{{ date('Y M d H:i:s', strtotime($directQueue->created_at)) }}</td>
                                                <td>
                                                    @if($directQueue->call_time)
                                                        {{ date('Y M d H:i:s', strtotime($directQueue->call_time)) }}
                                                    @elseif($directQueue->called_at)
                                                    {{ date('Y M d H:i:s', strtotime($directQueue->called_at)) }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>
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
                                                <td>
                                                    @if ($directQueue->called_at)
                                                    @php
                                                        $waktuCreate = \Carbon\Carbon::parse($directQueue->created_at);
                                                        $cek = $directQueue->call_time ? $directQueue->call_time : $directQueue->called_at;
                                                        $waktuPanggil = \Carbon\Carbon::parse($cek);
                                                        $durasiTunggu = $waktuPanggil ? $waktuPanggil->diff($waktuCreate) : null;
                                                        $formattedDurasiTunggu = $durasiTunggu 
                                                            ? sprintf('%02d:%02d:%02d', $durasiTunggu->h, $durasiTunggu->i, $durasiTunggu->s) 
                                                            : '-';
                                                        if($time_format == "inMinutes"){
                                                            $formattedDurasiTunggu = $waktuPanggil ? $waktuPanggil->diffInMinutes($waktuCreate) : '-';
                                                        }
                                                        
                                                    @endphp
                                                        {{ $formattedDurasiTunggu }} 
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($directQueue->call_time)
                                                    @php
                                                        $waktuPanggil = \Carbon\Carbon::parse($directQueue->call_time) ?: '';
                                                        $waktuSelesai = \Carbon\Carbon::parse($directQueue->done_at);
                                                        $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                                        $formattedDurasiLayanan = $durasiLayanan 
                                                        ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                                        : '-';
                                                        if($time_format == "inMinutes"){
                                                            $formattedDurasiLayanan =  $waktuPanggil ? $waktuPanggil->diffInMinutes($waktuSelesai) : '-';
                                                        }
                                                    @endphp
                                                    {{ $formattedDurasiLayanan }}
                                                    @elseif(!$directQueue->call_time && $directQueue->called_at)
                                                        @php
                                                            $waktuPanggil = \Carbon\Carbon::parse($directQueue->called_at);
                                                            $waktuSelesai = \Carbon\Carbon::parse($directQueue->done_at) ?: '';
                                                            $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                                            $formattedDurasiLayanan = $durasiLayanan 
                                                            ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                                            : '-';
                                                            if($time_format == "inMinutes"){
                                                                $formattedDurasiLayanan =  $waktuPanggil ? $waktuPanggil->diffInMinutes($waktuSelesai) : '-';
                                                            }
                                                        @endphp
                                                        {{ $formattedDurasiLayanan }}
                                                @else
                                                    -
                                                @endif
                                                </td>
                                                <td>
                                                @if ($directQueue->called_at)
                                                    @php
                                                    $waktuPanggil = \Carbon\Carbon::parse($directQueue->called_at);
                                                    $waktuSelesai = \Carbon\Carbon::parse($directQueue->done_at) ?: '';
                                                    $durasiLayanan = $waktuSelesai ? $waktuSelesai->diff($waktuPanggil) : null;
                                                    
                                                    if ($durasiLayanan && $durasiLayanan->h === 0 && $durasiLayanan->i === 0 && $durasiLayanan->s === 0) {
                                                        // Jika durasinya adalah 0, gunakan call_time
                                                        if ($directQueue->call_time) {
                                                            $waktuPanggil = \Carbon\Carbon::parse($directQueue->call_time) ?: '';
                                                            $waktuSelesai = \Carbon\Carbon::parse($directQueue->done_at);
                                                            $durasiLayanan = $waktuPanggil ? $waktuPanggil->diff($waktuSelesai) : null;
                                                        }
                                                    }

                                                    $formattedDurasiLayanan = $durasiLayanan 
                                                        ? sprintf('%02d:%02d:%02d', $durasiLayanan->h, $durasiLayanan->i, $durasiLayanan->s) 
                                                        : '-';
                                                    if($time_format == "inMinutes"){
                                                            $formattedDurasiLayanan =  $waktuPanggil ? $waktuPanggil->diffInMinutes($waktuSelesai) : '-';
                                                        }
                                                @endphp
                                                {{ $formattedDurasiLayanan }}
                                                @else
                                                    -
                                                @endif
                                                </td>
                                                <td>{{ $directQueue->Workstation ? $directQueue->Workstation->name : '-' }}
                                                </td>
                                                <td>{{ $directQueue->Service->name }}</td>
                                                <td>{{ $directQueue->subService->name ?? '-' }}</td>
                                                <td>
                                                    @if ($directQueue->NewService)
                                                        {{ $directQueue->NewService->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                
                                                <td>{{  $directQueue->Vct ? $directQueue->Vct->name : '-' }}
                                                </td>
                                                <td>{{ __(ucwords($directQueue->status)) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">{{ __('No data') }}</td>
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
            "buttons": [{
                    extend: 'excelHtml5',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ $start_date ? '(' . $start_date . ')' : '' }} - {{ $end_date ? '(' . $end_date . ')' : '' }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ $start_date ? '(' . $start_date . ')' : '' }} - {{ $end_date ? '(' . $end_date . ')' : '' }}"
                },
                {
                    extend: 'print',
                    text: 'Cetak',
                    title: "Antrian Onsite {{ Auth::user()->Branch->name }} {{ $start_date ? '(' . $start_date . ')' : '' }} - {{ $end_date ? '(' . $end_date . ')' : '' }}"
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

        function toggleCheckbox() {
            const checkbox = document.getElementById('formatTime');
            checkbox.checked = !checkbox.checked; // Toggle the checked state
        }
    </script>
@endpush
