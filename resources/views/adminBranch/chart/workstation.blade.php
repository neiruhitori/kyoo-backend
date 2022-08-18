@extends('layouts.app')

@section('content')
<form id="filter_form" class="mb-4">
    <div class="row">
        <div class="col" style="max-width: 300px;">
            <div class="form-group">
                <label for="department_id">Departemen</label>
                <select
                    class="form-control"
                    id="department_id"
                    autocomplete="off"
                >
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="workstation_id">Meja</label>
                <select
                    class="form-control"
                    id="workstation_id"
                    autocomplete="off"
                >
                    @foreach ($workstations as $workstation)
                        <option value="{{ $workstation->id }}">{{ $workstation->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status Antrian</label>
                <select
                    class="form-control"
                    id="status"
                    autocomplete="off"
                >
                    <option value="end served">Dilayani</option>
                    <option value="no show">Tidak Hadir</option>
                </select>
            </div>
        </div>

        <div class="col" style="max-width: 300px;">
            <div class="form-group">
                <label for="report_type">Jenis Laporan</label>
                <select
                    class="form-control"
                    id="report_type"
                    autocomplete="off"
                >
                    <option value="hourly">Per Jam</option>
                    <option value="daily">Harian</option>
                    <option value="monthly">Bulanan</option>
                </select>
            </div>
    
            <div class="form-group" id="hourly_type">
                <label for="date">Tanggal</label>
                <input type="date" class="form-control" autocomplete="off" name="date" id="date" value={{ date('Y-m-d') }}>
            </div>

            <div class="form-group d-none" id="monthly_type">
                <label for="date">Tahun</label>
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
    
            <div class="form-row d-none" id="daily_type">
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
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah Antrian Meja</h6>
            </div>

            <div class="card-body">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Persentase Antrian Meja</h6>
            </div>

            <div class="card-body">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let reportType = $("#report_type").val(),
        status = $("#status").val()

    $(document).ready(async function () {
        const data = await fetchData()

        const barChart = initBarChart($("#barChart"), data)
        const pieChart = initPieChart($("#pieChart"), data)

        $("#report_type").change(function (e) {
            reportType = e.target.value

            toggleFilterUI()
        })

        $("#filter_form").submit(async function (e) {
            e.preventDefault()

            const data = await fetchData()

            updateBarChart(barChart, data)
            updatePieChart(pieChart, data)
        })

        $("#department_id").change(async function (e) {
            $("#workstation_id").html(["<option value disabled selected>Loading...</option>"])
            
            const res = await axios.get(`/api/workstation/department/${e.target.value}`)

            console.log(res)

            const opts = res.data.map(v => {
                return `<option value="${v.id}">
                    ${v.name}
                </option>`
            })

            $("#workstation_id").html(opts)
        })

        $("#status").change(function (e) {
            status = e.target.value
        })
    })

    function toggleFilterUI() {
        if (reportType === 'hourly') {
            $("#hourly_type").removeClass('d-none')

            $("#daily_type").addClass('d-none')
            $("#monthly_type").addClass('d-none')
        }

        if (reportType === 'daily') {
            $("#daily_type").removeClass('d-none')

            $("#hourly_type").addClass('d-none')
            $("#monthly_type").addClass('d-none')
        }

        if (reportType === 'monthly') {
            $("#monthly_type").removeClass('d-none')

            $("#hourly_type").addClass('d-none')
            $("#daily_type").addClass('d-none')
        }
    }

    async function fetchData() {
        const params = getParams()

        return await $.ajax('{{ route('admin-branch.report.workstation.chart.all') }}', {
            method: 'GET',
            dataType: 'json',
            data: params
        })
    }

    function getParams() {
        const params = {
            workstation_id: $("#workstation_id").val(),
            status: $("#status").val(),
            report_type: $("#report_type").val()
        }

        if (reportType === 'hourly') {
            params.date = $("#date").val()
        }

        if (reportType === 'daily') {
            params.month = $("#month").val()
            params.year = $("#year").val()
        }

        if (reportType === 'monthly') {
            params.year = $("#year").val()
        }

        return params
    }

    function initBarChart(ctx, items) {
        const { labels, data } = transformBarData(items)

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Total Dilayani",
                        backgroundColor: "#4472c4",
                        borderWidth: 1,
                        data
                    }
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            suggestedMax: 5,
                            precision: 0
                        }
                    }]
                }
            }
        })
    }

    function initPieChart(ctx, items) {
        const { labels, data } = transformPieData(items)

        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels, 
                datasets: [
                    {
                        backgroundColor: ['#70ad47', '#a61c00'],
                        data
                    }
                ]
            },
            options: {
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let label = data.datasets[tooltipItem.datasetIndex].label || '';

                            const total = data.datasets[tooltipItem.datasetIndex].data.reduce(function (acc, item) {
                                return acc += item
                            }, 0)

                            const value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]

                            if (label) {
                                label += ': '
                            }

                            label += Math.round(value * 100 / total);

                            label += '%'
                            
                            return label;
                        }
                    }
                }
            }
        })
    }

    function updateBarChart(ctx, items) {
        const { labels, data } = transformBarData(items)

        ctx.data.labels = labels
        ctx.data.datasets[0].label = status === 'no show' ? 'Total Tidak Hadir' : 'Total Dilayani'
        ctx.data.datasets[0].data = data
        ctx.update()
    }

    function updatePieChart(ctx, items) {
        const { labels, data } = transformPieData(items)

        ctx.data.labels = labels
        ctx.data.datasets[0].data = data
        ctx.update()
    }

    function transformBarData(items) {
        if (reportType === 'hourly') {
            return getHourlyData(items)
        }

        if (reportType === 'daily') {
            return getDailyData(items)
        }

        if (reportType === 'monthly') {
            return getMonthlyData(items)
        }
    }

    function getHourlyData(items) {
        const labels = [],
            data = []

        for (let i = 0; i < 24; i++) {
            const item = items.find(v => {
                return v.hour == i
            })

            let label = i + ':00'
            if (i < 10) {
                label = `0${i}:00`
            }

            labels[i] = label

            data[i] = 0
            if (item) {
                data[i] = status === 'no show'
                    ? item.total_no_show
                    : item.total_served
            }
        }

        return { labels, data }
    }

    function getDailyData(items) {
        const labels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu']
        const data = []

        for (let i = 0; i < labels.length; i++) {
            const item = items.find(v => v.day == i)

            data[i] = 0

            if (item) {
                data[i] = status !== 'no show'
                    ? item.total_served
                    : item.total_no_show
            }
        }

        return { labels, data }
    }

    function getMonthlyData(items) {
        const labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            data = []

        for (let i = 0; i < labels.length; i++) {
            const item = items.find(v => v.month == i)

            data[i] = 0

            if (item) {
                data[i] = status !== 'no show'
                    ? item.total_served
                    : item.total_no_show
            }
        }

        return { labels, data }
    }

    function transformPieData(items) {
        const labels = ['Dilayani', 'Tidak Hadir'],
            data = []

        // served
        const totalServed = items.reduce(function (acc, item) {
            return acc += item.total_served
        }, 0)
        data.push(totalServed)

        // no show
        const totalNoShow = items.reduce(function (acc, item) {
            return acc += item.total_no_show
        }, 0)
        data.push(totalNoShow)

        return { labels, data }
    }
</script>
@endpush