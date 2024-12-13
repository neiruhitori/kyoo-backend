@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <form id="filterForm" class="mb-4">
                <div class="form-row align-items-end">
                    <div class="col-auto">
                        <label for="deparmentId">Departemen</label>
                        <select class="form-control" id="departmentId" style="width: 180px;" autocomplete="off">
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monitoring Departemen</h6>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <label for="refreshInterval" class="mr-3 mb-0" style="white-space: nowrap;">Refresh / menit</label>
                            <select class="form-control" autocomplete="off" id="refreshInterval" style="max-width: 70px">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                            </select>
                        </div>

                        <div class="ml-3 badge badge-secondary" style="font-size: .875rem;">
                            <span class="fas fa-redo"></span>
                            <span class="ml-2" id="refreshedAt">00:00:00</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-4" id="table">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">Nama Layanan</th>
                                    <th rowspan="2" class="align-middle text-right">Menunggu</th>
                                    <th rowspan="2" class="align-middle text-right">Dilayani</th>
                                    <th rowspan="2" class="align-middle text-right">Tidak Hadir</th>
                                    <th colspan="3" class="text-center">Waktu Tunggu</th>
                                    <th colspan="3" class="text-center">Waktu Melayani</th>
                                </tr>
    
                                <tr>
                                    {{-- Waktu Tunggu Child Header --}}
                                    <th class="text-center">Saat Ini</th>
                                    <th class="text-center">Rata-Rata</th>
                                    <th class="text-center">Terlama</th>
    
                                    {{-- Waktu Melayani Child Header --}}
                                    <th class="text-center">Saat Ini</th>
                                    <th class="text-center">Rata-Rata</th>
                                    <th class="text-center">Terlama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center">Data tidak ditemukan.</td>
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
    let departmentId,
        refreshDuration = $("#refreshInterval").val()

    $(document).ready(function () {
        setSelectedDepartment()
        feedTable()
        setLastRefreshedAt()

        $("#filterForm").submit(function (e) {
            e.preventDefault()

            departmentId = $("#departmentId").val()
            feedTable()
        })

        $("#refreshInterval").change(function (e) {
            refreshDuration = e.target.value
        })

        setTimeout(function () {
            handleTimeout()
        }, refreshDuration * 60000)
    })

    function handleTimeout() {
        setLastRefreshedAt()
        feedTable()

        setTimeout(function () {
            handleTimeout()
        }, refreshDuration * 60000)
    }

    function setLastRefreshedAt() {
        localStorage.setItem('lastRefreshedAt', new Date().toISOString())
        $("#refreshedAt").text(refreshedAtColumn(new Date()))
    }

    function setSelectedDepartment() {
        departmentId = $("#departmentId").val()
    }

    async function feedTable() {
        const res = await axios.get('/admin-branch/monitoring/department/' + departmentId)
            .then(res => res.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="10" class="text-center">Data tidak ditemukan.</td>
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
                <td class="text-right">${v.total_waiting}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${formatTime(v.now_waiting_duration)}</td>
                <td class="text-center">${formatTime(v.avg_waiting_duration)}</td>
                <td class="text-center"><a href="/admin-branch/monitoring/department-detail/${v.id}/max-wait">${formatTime(v.max_waiting_duration)}</a></td>
                <td class="text-center">${formatTime(v.now_served_duration)}</td>
                <td class="text-center">${formatTime(v.avg_served_duration)}</td>
                <td class="text-center"><a href="/admin-branch/monitoring/department-detail/${v.id}/max-service">${formatTime(v.max_served_duration)}</a></td>
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

    function refreshedAtColumn(date) {
        let hours = date.getHours(),
            minutes = date.getMinutes(),
            seconds = date.getSeconds()
        
        if (hours < 10) hours = '0' + hours
        if (minutes < 10) minutes = '0' + minutes
        if (seconds < 10) seconds = '0' + seconds

        return hours + ':' + minutes + ':' + seconds
    }
</script>
@endpush