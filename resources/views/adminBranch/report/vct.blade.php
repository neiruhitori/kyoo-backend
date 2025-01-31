@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <form id="filterForm" class="mb-4" style="width: 100%; max-width: 300px;">
                <div class="form-group">
                    <label for="report_type">{{ __('Report Type') }}</label>
                    <select
                        class="form-control"
                        id="report_type"
                        autocomplete="off"
                    >
                        <option value="monthly">{{ __('Monthly') }}</option>
                        <option value="daily">{{ __('Daily') }}</option>
                    </select>
                </div>

                <div class="form-group d-none" id="daily_type">
                    <label for="date">{{ __('Date') }}</label>
                    <input type="date" class="form-control" autocomplete="off" name="date" id="date" value={{ date('Y-m-d') }}>
                </div>

                <div class="form-row" id="monthly_type">
                    <div class="form-group col-md-6">
                        <label for="month">{{ __('Select Month') }}</label>
                        <select
                            class="form-control"
                            id="month"
                            autocomplete="off"
                        >
                        <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>{{ __('January') }}</option>
                        <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>{{ __('February') }}</option>
                        <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>{{ __('March') }}</option>
                        <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>{{ __('April') }}</option>
                        <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>{{ __('May') }}</option>
                        <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>{{ __('June') }}</option>
                        <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>{{ __('July') }}</option>
                        <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>{{ __('August') }}</option>
                        <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>{{ __('November') }}</option>
                        <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>{{ __('December') }}</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="year">{{ __('Select Year') }}</label>
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

                <div class="form-group">
                    <label for="report_type">{{ __('Department') }}</label>
                    <select
                        class="form-control"
                        id="department_id"
                        autocomplete="off"
                    >
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('User Report') }}</h6>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <a href="{{ route('admin-branch.report.vct.chart') }}" class="btn btn-outline-dark">
                                <span class="fas fa-chart-pie mr-2"></span>Chart
                            </a>
                        </div>

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
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('Virtual Counter') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('Service') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Total Queue') }}</th>
                                    <th rowspan="2" class="align-middle text-center">{{ __('Operational Hours') }}</th>
                                    <th rowspan="2" class="align-middle text-center">{{ __('Total Serving Time') }}</th>
                                    <th rowspan="2" class="align-middle text-center">{{ __('Total Idle Time') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Productivity') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Waiting Time') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Served Time') }}</th> 
                                </tr>
    
                                <tr>
                                  {{-- Total Antrian Child Header --}}
                                  <th class="text-right align-middle">{{ __('Total Tickets') }}</th>
                                  <th class="text-right align-middle">{{ __('Serve') }}</th>
                                  <th class="text-right align-middle">{{ __('No Show') }}</th>

                                  {{-- Waktu Tunggu Child Header --}}
                                  <th class="text-center align-middle">{{ __('Fastest') }}</th>
                                  <th class="text-center align-middle">{{ __('Average') }}</th>
                                  <th class="text-center align-middle">{{ __('Longest') }}</th>
  
                                  {{-- Waktu Melayani Child Header --}}
                                  <th class="text-center align-middle">{{ __('Fastest') }}</th>
                                  <th class="text-center align-middle">{{ __('Average') }}</th>
                                  <th class="text-center align-middle">{{ __('Longest') }}</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <tr>
                                    <td colspan="15" class="text-center">Data tidak ditemukan.</td>
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
            window.open("{{ route('admin-branch.report.vct.pdf') }}?" + new URLSearchParams(filter))
        })

        $("#excelButton").click(function (e) {
            window.open("{{ route('admin-branch.report.vct.excel') }}?" + new URLSearchParams(filter))
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

        filter.department_id = $("#department_id").val()
    }

    async function feedTable() {
        setFilterValue()

        const res = await axios.get('/admin-branch/report/vct/all', {
            params: filter
        })
            .then(res => res.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="15" class="text-center">Data tidak ditemukan.</td>
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
            console.log(v.services)

            return `<tr>
                <td>${v.name}</td>
                <td>${v.services.map(v => v.name).join(', ')}</td>
                <td class="text-right">${v.total_queue}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${formatTime(v.total_operating_duration)}</td>
                <td class="text-center">${formatTime(v.total_serve_duration)}</td>
                <td class="text-center">${formatTime(v.total_idle_duration)}</td>
                <td class="text-right">${v.productivity_percentage.toFixed(2) + '%'}</td>
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