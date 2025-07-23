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
             @if (session('error'))
                        @include('layouts.alert')
            @endif
            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3">
                </div> --}}
                
                <div class="card-body">
                    <h5 class="m-0 font-weight-bold " style="color: #103c7c">{{ __('Monthly Report') }}</h5>
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
                                    <button class="btn btn-primary mt-3" style="background-color: #103C7C">{{ __('Filter') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
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
                                        @forelse ($data as $directQueue)
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
                                            <td>{{ $directQueue->requeue_count > 0 ? 'Antri Ulang' : '-' }}</td>
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
            const workstation_service_idOldValue = '{{ $workstation_service_id }}';

            $('#workstation_service_id').val(workstation_service_idOldValue);
        });
        $('#dataTable').dataTable({
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
