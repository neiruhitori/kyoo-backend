@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3>Laporan Kepuasan Pelanggan</h3>
</div>

<form class="mb-4">
    <div class="form-row align-items-end">
        <div class="col-auto">
            <label for="branch_id">Cabang</label>
            <select
                id="branch_id"
                name="branch_id"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                <option value selected>Semua Cabang</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branch->id != $branch_id ?: 'selected' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <label for="month">Bulan</label>
            <select
                id="month"
                name="month"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month != $i ?: 'selected' }}>
                        {{ date('F', strtotime('2020-' . $i . '-01')) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-auto">
            <label for="year">Tahun</label>
            <select
                id="year"
                name="year"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 2000; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $year != $i ?: 'selected' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th class="text-right">Total Antrian</th>
                        <th class="text-right">Total Feedback</th>
                        <th class="text-right">Persentasi Feedback</th>
                        <th>Skor Rata-Rata Ulasan</th>   
                    </tr>
                </thead>

                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

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