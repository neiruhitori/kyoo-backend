@extends('layouts.app')

@section('content')
<style>
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

                <button type="submit" class="btn btn-primary" style="background-color: #103c7c">Filter</button>
            </form>

            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3"> --}}
                    {{-- </div> --}}
                    
                    <div class="card-body">
                    <h5 class="mb-4 font-weight-bold " style="color: #103C7C">{{ __('Waiting Service Distribution Report') }}</h5>
                    <div class="mb-3">
                        <button id="pdfButton" class="btn btn-outline-dark mr-1">
                            <span class="fas fa-file-pdf mr-2"></span>PDF
                        </button>

                        <button id="excelButton" class="btn btn-outline-dark">
                            <span class="fas fa-file-excel mr-2"></span>Excel
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('Service') }}</th>
                                    <th colspan="14" class="text-center">{{ __('Tickets in Interval (Minutes)') }}</th>
                                    <th rowspan="2" class="align-middle text-right">Total</th>
                                </tr>
    
                                <tr>
                                    <th class="text-center" colspan="2">0-5</th>
                                    <th class="text-center" colspan="2">5-10</th>
                                    <th class="text-center" colspan="2">10-15</th>
                                    <th class="text-center" colspan="2">15-20</th>
                                    <th class="text-center" colspan="2">20-25</th>
                                    <th class="text-center" colspan="2">25-30</th>
                                    <th class="text-center" colspan="2">>=30</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <tr>
                                    <td colspan="16" class="text-center">{{ __('Data not Found') }}</td>
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

        console.log($("#department_id").val())

        $("#filterForm").submit(function (e) {
            e.preventDefault()

            feedTable()
        })

        $("#pdfButton").click(function (e) {
            window.open("{{ route('admin-branch.report.service-distribution.pdf') }}?" + new URLSearchParams(filter))
        })

        $("#excelButton").click(function (e) {
            window.open("{{ route('admin-branch.report.service-distribution.excel') }}?" + new URLSearchParams(filter))
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

        const res = await axios.get('/admin-branch/report/service-distribution/all', {
            params: filter
        })
            .then(res => res.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        console.log(res)
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="16" class="text-center">{{ __('Data not Found') }}</td>
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
                <td class="text-center">${v._0_5}</td>
                <td class="text-right">${v._0_5_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._5_10}</td>
                <td class="text-right">${v._5_10_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._10_15}</td>
                <td class="text-right">${v._10_15_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._15_20}</td>
                <td class="text-right">${v._15_20_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._20_25}</td>
                <td class="text-right">${v._20_25_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._25_30}</td>
                <td class="text-right">${v._25_30_percentage.toFixed(2) + '%'}</td>
                <td class="text-center">${v._30_}</td>
                <td class="text-right">${v._30__percentage.toFixed(2) + '%'}</td>
                <td class="text-right">${v.total}</td>
            </tr>`
        })
    }
</script>
@endpush