@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3>Laporan Cabang</h3>
</div>

<form class="mb-4" id="filter_form">
    <div class="form-row align-items-end">
        <div class="col-auto">
            <label for="branch_id">Cabang</label>
            <select
                name="branch_id"
                class="form-control"
                style="width: 180px;"
                id="branch_id"
                autocomplete="off"
            >
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <label for="report_type">Jenis Laporan</label>
            <select
                name="report_type"
                id="report_type"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                <option value="monthly" selected>Bulanan</option>
                <option value="daily">Harian</option>
            </select>
        </div>

        <div class="col-auto d-none daily-input">
            <label for="date">Tanggal</label>
            <input
                type="date"
                class="form-control"
                autocomplete="off"
                name="date"
                id="date"
                value="{{ date('Y-m-d') }}"
            >
        </div>
        
        <div class="col-auto monthly-input">
            <label for="month">Bulan</label>
            <select
                id="month"
                name="month"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $i != date('n') ?: 'selected' }}>
                        {{ date('F', strtotime('2020-' . $i . '-01')) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-auto monthly-input">
            <label for="year">Tahun</label>
            <select
                id="year"
                name="year"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 2000; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $i != date('Y') ?: 'selected' }}>
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
        <div class="d-flex justify-content-between mb-3">
            {{-- <div>
                <a href="{{ route('adminCorporate.report.service.chart') }}" class="btn btn-outline-dark">
                    <span class="fas fa-chart-pie mr-2"></span>Chart
                </a>
            </div> --}}

            <div>
                <button id="pdfButton" class="btn btn-outline-dark mr-1">
                    <span class="fas fa-file-pdf mr-2"></span>PDF
                </button>

                <button id="excelButton" class="btn btn-outline-dark mr-1">
                    <span class="fas fa-file-excel mr-2"></span>Excel
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0" id="table">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Layanan</th>
                        <th colspan="3" class="text-center">Total Antrian</th>
                        <th colspan="3" class="text-center">Waktu Tunggu</th>
                        <th colspan="3" class="text-center">Waktu Dilayani</th> 
                    </tr>

                    <tr>
                        <th class="text-right">Jumlah Tiket</th>
                        <th class="text-right">Dilayani</th>
                        <th class="text-right">Tidak Hadir</th>

                        <th class="text-center">Tercepat</th>
                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>

                        <th class="text-center">Tercepat</th>
                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="10" class="text-center">Data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    toggleMonthInput($("#report_type").val())

    $(function () {
        feedTable()

        $("#report_type").change(e => toggleMonthInput(e.target.value))

        $("#filter_form").submit(function (e) {
            e.preventDefault()
            feedTable()
        })

        $("#pdfButton").click(function (e) {
            const filter = getFilterValue($("#report_type").val())
            window.open("{{ route('adminCorporate.report.service.pdf') }}?" + new URLSearchParams(filter))
        })

        $("#excelButton").click(function (e) {
            const filter = getFilterValue($("#report_type").val())
            window.open("{{ route('adminCorporate.report.service.excel') }}?" + new URLSearchParams(filter))
        })
    })

    function toggleMonthInput(reportType) {
        if (reportType === 'daily') {
            $(".monthly-input").addClass('d-none')
            $(".daily-input").removeClass('d-none')
            return
        }

        $(".monthly-input").removeClass('d-none')
        $(".daily-input").addClass('d-none')
    }

    async function feedTable() {
        const filter = getFilterValue($("#report_type").val())

        const res = await axios.get('/admin-corporate/report/service/all', {
            params: filter
        })
            .then(res => res.data)
            .catch(err => [])
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="10" class="text-center">Data tidak ditemukan</td>
            </tr>`)
            return
        }
        
        // Append response to table UI
        $("#table tbody").html(transformResponse(res))
    }

    function transformResponse(data) {
        return data.map(v => {
            return `<tr>
                <td>${v.service.name}</td>
                <td class="text-right">${v.total_queue}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${v.shortest_wait_duration}</td>
                <td class="text-center">${v.average_wait_duration}</td>
                <td class="text-center">${v.longest_wait_duration}</td>
                <td class="text-center">${v.shortest_serve_duration}</td>
                <td class="text-center">${v.average_serve_duration}</td>
                <td class="text-center">${v.longest_serve_duration}</td>
            </tr>`
        })
    }

    function getFilterValue(reportType) {
        const filter = {}

        if (reportType === 'daily') filter.date = $("#date").val()

        if (reportType === 'monthly') {
            filter.month = $("#month").val()
            filter.year = $("#year").val()
        }

        filter.branch_id = $("#branch_id").val()

        return filter
    }
</script>
@endpush