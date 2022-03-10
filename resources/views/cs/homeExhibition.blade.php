@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User {{ __('Virtual Counter') }} {{ Auth::user()->Branch->name }}</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
        </div>

        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Active Exhibition') }}</h6>
                </div>

                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col text-right">
                            <a href="{{ route('cs.exhibition.create') }}">
                                <button class="btn btn-primary">
                                    Daftar Exhibition
                                </button>
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Queue Number') }}</th>
                                    <th>{{ __('Booking Code') }}</th>
                                    <th>{{ __('Booking Time') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Service') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($unfinishedQueue as $queue)
                                    <tr>
                                        <td>{{ $queue->queue_order }}</td>
                                        <td>{{ $queue->booking_code }}</td>
                                        <td>{{ $queue->Slot->start_time }} - {{ $queue->Slot->end_time }}</td>
                                        <td>{{ $queue->name }}</td>
                                        <td>{{ $queue->Slot->Service->name }}</td>
                                        <td>
                                            @switch($queue->status)
                                                @case('book')
                                                    <span class="badge badge-primary">{{ __('Book') }}</span>
                                                    @break

                                                @case('check in')
                                                    <span class="badge badge-success">{{ __('Attend') }}</span>
                                                    @break     
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($queue->status)
                                                @case('book')
                                                    <div class="row">
                                                        <form action="{{ route('cs.exhibition.update', $queue->id) }}" method="post">
                                                            @csrf
                                                            @method('PUT')

                                                            <input type="hidden" name="status" value="end served">
                                                            <button
                                                                class="btn btn-success mx-2"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="Check In"
                                                            >
                                                                Check In
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('cs.exhibition.update', $queue->id) }}" method="post">
                                                            @csrf
                                                            @method('PUT')

                                                            <input type="hidden" name="status" value="no show">
                                                            <button
                                                                class="btn btn-danger"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{ __('No Show') }}"
                                                            >
                                                                {{ __('No Show') }}
                                                            </button>
                                                        </form>
                                                    </div>
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
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Antrian</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>{{ __('Queue Number') }}</th>
                                <th>{{ __('Booking Code') }}</th>
                                <th>{{ __('Booking Time') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Service') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>

                            @foreach ($finishedQueue as $index => $item)
                                <tr>
                                    <td>{{$item->queue_order}}</td>
                                    <td>{{$item->booking_code}}</td>
                                    <td>{{$item->Slot->start_time}} - {{$item->Slot->end_time}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->Slot->Service->name}}</td>
                                    <th>
                                        @switch($item->status)
                                            @case('end served')
                                                <span class="badge badge-success">{{ __('End Served') }}</span>
                                                @break

                                            @case('no show')
                                                <span class="badge badge-danger">{{ __('No Show') }}</span>
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
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush