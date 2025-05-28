@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <form id="filterForm" class="mb-4">
                <div class="form-row align-items-end">
                    <div class="col-auto">
                        <label for="deparmentId">{{ __('Department') }}</label>
                        <select class="form-control" id="departmentId" style="width: 180px;" autocomplete="off">
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="serviceId">{{ __('Service') }}</label>
                        <select class="form-control" id="serviceId" style="width: 180px;" autocomplete="off">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
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
                    <h6 class="m-0 font-weight-bold text-primary">Monitoring {{ __('Service') }}</h6>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <label for="refreshInterval" class="mr-3 mb-0" style="white-space: nowrap;">Refresh / {{ __('Minutes') }}</label>
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
                                    <th rowspan="2" class="align-middle">{{ __('Workstation') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('Status') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('Staff') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Waiting') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Serve') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('No Show') }}</th>
                                    <th rowspan="2" class="align-middle text-center">{{ __('Operational Hours') }}</th>
                                    <th colspan="2" class="text-center">{{ __('Service Time') }}</th>
                                </tr>
    
                                <tr>
                                    <th class="text-center">{{ __('Currently') }}</th>
                                    <th class="text-center">{{ __('Average') }}</th>
                                </tr>
                            </thead>
    
                            <tbody>
                                <tr>
                                    <td colspan="10" class="text-center">{{ __('Data not Found') }}</td>
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
    let serviceId,
        refreshDuration = $("#refreshInterval").val()

    $(document).ready(function () {
        setSelectedFilter()
        feedTable()
        setLastRefreshedAt()

        $("#filterForm").submit(function (e) {
            e.preventDefault()

            setSelectedFilter()
            feedTable()
        })

        $("#departmentId").change(async function (e) {
            $("#serviceId").html(["<option value disabled selected>Loading...</option>"])
            
            const res = await axios.get(`/cs/monitoring/department/${e.target.value}/service`)

            const serviceOpts = res.data.map(v => {
                return `<option value="${v.id}">
                    ${v.name}
                </option>`
            })

            $("#serviceId").html(serviceOpts)
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

    function setSelectedFilter() {
        serviceId = $("#serviceId").val()
    }

    async function feedTable() {
        const res = await axios.get('/cs/monitoring/service/' + serviceId)
            .then(res => res.data)
            .catch(err => {
                console.error(err)
                
                return []
            })
        
        if (!res.length) {
            $("#table tbody").html(`<tr>
                <td colspan="9" class="text-center">{{ __('Data not Found') }}</td>
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
            const badgeEl = `<span class="badge badge-${v.is_online ? 'success' : 'danger'}">
                ${v.is_online ? 'Online' : 'Offline'}
            </span>`

            return `<tr>
                <td>${v.name}</td>
                <td>${badgeEl}</td>
                <td>${v.user.name}</td>
                <td class="text-right">${v.total_waiting}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${formatTime(v.now_operation_duration)}</td>
                <td class="text-center">${formatTime(v.now_waiting_duration)}</td>
                <td class="text-center">${formatTime(v.avg_waiting_duration)}</td>
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