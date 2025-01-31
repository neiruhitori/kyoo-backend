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
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Customer Satisfaction Report') }}</h6>
            </div>

            <div class="card-body">
                <form action="" method="GET" class="mb-4" style="max-width: 500px;">
                    <div class="row">
                        <div class="form-group col-xs-12 col-md-6">
                            <label>{{ __('Select Month') }}</label>
                            <select name="month" class="form-control">
                                <option disabled selected>--- {{ __('Select Month') }} ---</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month != $i ?: 'selected' }}>
                                        {{ date('F', strtotime('1999-' . $i . '-01')) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-md-6">
                            <label>{{ __('Select Year') }}</label>
                            <select name="year" class="form-control">
                                <option disabled selected>--- {{ __('Select Year') }} ---</option>
                                @for ($i = 2000; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $year != $i ?: 'selected' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('Date') }}</th>
                            <th class="text-right">{{ __('Total Queue') }}</th>
                            <th class="text-right">{{ __('Total Feedback') }}</th>
                            <th class="text-right">{{ __('Feedback Percentage') }}</th>
                            <th>{{ __('Average Review Score') }}</th>   
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($reports) > 0) 
                            @foreach ($reports as $report)
                                <tr>
                                    <td class="text-center">{{ $report->date }}</td>
                                    <td class="text-right">{{ $report->total_queue }}</td>
                                    <td class="text-right">{{ $report->total_feedback }}</td>
                                    <td class="text-right">{{ $report->feedback_percentage }}%</td>
                                    <td>
                                        <span class="fas fa-star mr-1 text-warning"></span>
                                        {{ $report->average_rate }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">{{ __('Data not Found') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
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
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
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
            },  
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: "Laporan Kepuasan Pelanggan {{ date('F', $month) }} {{ $year }}"
                },
                {
                    extend: 'pdfHtml5',
                    title: "Laporan Kepuasan Pelanggan {{ date('F', $month) }} {{ $year }}"
                },
                {
                    extend: 'print',
                    text: 'Cetak',
                    title: "Laporan Kepuasan Pelanggan {{ date('F', $month) }} {{ $year }}"
                }
            ]
        })
    </script>
@endpush