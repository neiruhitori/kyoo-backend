@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <form id="filterForm" class="mb-4" style="width: 100%; max-width: 300px;">
                <div class="form-group">
                    <label for="report_type">Jenis Laporan</label>
                    <select
                        class="form-control"
                        id="report_type"
                        autocomplete="off"
                    >
                        <option value="monthly">Bulanan</option>
                        <option value="daily">Harian</option>
                    </select>
                </div>

                <div class="form-group d-none" id="daily_type">
                    <label for="date">Tanggal</label>
                    <input type="date" class="form-control" autocomplete="off" name="date" id="date" value={{ date('Y-m-d') }}>
                </div>

                <div class="form-row" id="monthly_type">
                    <div class="form-group col-md-6">
                        <label for="month">Bulan</label>
                        <select
                            class="form-control"
                            id="month"
                            autocomplete="off"
                        >
                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="year">Tahun</label>
                        <select
                            class="form-control"
                            id="year"
                            autocomplete="off"
                        >
                            @for ($year = date('Y'); $year >= 2005; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan Departemen</h6>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <button id="pdfButton" class="btn btn-outline-dark mr-1">
                            <span class="fas fa-file-pdf mr-2"></span>PDF
                        </button>

                        <button id="excelButton" class="btn btn-outline-dark mr-1">
                            <span class="fas fa-file-excel mr-2"></span>Excel
                        </button>

                        <a href="{{ route('admin-branch.report.department.chart') }}" class="btn btn-outline-dark">
                            <span class="fas fa-chart-pie mr-2"></span>Chart
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">Departemen</th>
                                    <th rowspan="2" class="align-middle">Meja</th>
                                    <th colspan="3" class="text-center">Total Antrian</th>
                                    <th colspan="3" class="text-center">Waktu Tunggu</th>
                                    <th colspan="3" class="text-center">Waktu Dilayani</th> 
                                </tr>
    
                                <tr>
                                    {{-- Total Antrian Child Header --}}
                                    <th class="text-right">Jumlah Tiket</th>
                                    <th class="text-right">Dilayani</th>
                                    <th class="text-right">Tidak Hadir</th>

                                    {{-- Waktu Tunggu Child Header --}}
                                    <th class="text-center">Tercepat</th>
                                    <th class="text-center">Rata-Rata</th>
                                    <th class="text-center">Terlama</th>
    
                                    {{-- Waktu Melayani Child Header --}}
                                    <th class="text-center">Tercepat</th>
                                    <th class="text-center">Rata-Rata</th>
                                    <th class="text-center">Terlama</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <tr>
                                    <td colspan="11" class="text-center">Data tidak ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    let filter = {}
    let reportType = $("#report_type").val()

    $(document).ready(function () {
        feedTable()

        $("#filterForm").submit(function (e) {
            e.preventDefault()

            feedTable()
        })

        $("#pdfButton").click(function (e) {
            window.open("{{ route('admin-branch.report.department.pdf') }}?" + new URLSearchParams(filter))
        })

        $("#excelButton").click(function (e) {
            window.open("{{ route('admin-branch.report.department.excel') }}?" + new URLSearchParams(filter))
        })

        $("#report_type").change(function (e) {
            reportType = e.target.value

            setFilterValue()

            if (reportType === 'monthly') {
                $("#daily_type").addClass('d-none')
                $("#monthly_type").removeClass('d-none')
                return
            }

            if (reportType === 'daily') {
                $("#monthly_type").addClass('d-none')
                $("#daily_type").removeClass('d-none')
                return
            }
        })
    })

    function setFilterValue() {
        filter = {}

        if (reportType === 'daily') {
            filter.date = $("#date").val()
        }

        if (reportType === 'monthly') {
            filter.month = $("#month").val()
            filter.year = $("#year").val()
        }
    }

    async function feedTable() {
        setFilterValue()

        const res = await axios.get('/admin-branch/report/department/all', {
            params: filter
        })
            .then(res => res.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="11" class="text-center">Data tidak ditemukan.</td>
            </tr>`)
            return
        }
        
        // Append response to table UI
        $("#table tbody").html(
            transformResponse(res)
        )
    }

    function transformResponse(data) {
        return data.map(v => {
            return `<tr>
                <td>${v.name}</td>
                <td>${v.workstations.map(v => v.name).join(', ')}</td>
                <td class="text-right">${v.total_queue}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${formatTime(v.shortest_wait_duration)}</td>
                <td class="text-center">${formatTime(v.average_wait_duration)}</td>
                <td class="text-center">${formatTime(v.longest_wait_duration)}</td>
                <td class="text-center">${formatTime(v.shortest_serve_duration)}</td>
                <td class="text-center">${formatTime(v.average_serve_duration)}</td>
                <td class="text-center">${formatTime(v.longest_serve_duration)}</td>
            </tr>`
        })
    }

    function formatTime(value) {
        let hours = Math.floor(value / 3600),
            minutes = Math.floor((value % 3600) / 60),
            seconds = Math.floor(value % 3600 % 60)
        
        if (hours < 10) hours = '0' + hours
        if (minutes < 10) minutes = '0' + minutes
        if (seconds < 10) seconds = '0' + seconds

        return hours + ':' + minutes + ':' + seconds
    }
</script>
@endpush