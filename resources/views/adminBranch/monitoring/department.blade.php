@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-7">
            <form id="filterForm" class="mb-5">
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
                        <button type="submit" class="btn" style="background-color: #103C7C; color: #fff;">Filter</button>
                    </div>
                </div>
            </form>

            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3">
                    
                </div> --}}

                <div class="">
                    <div class="d-flex justify-content-between align-items-center p-4">

                        <h5 class="m-0 font-weight-bold" style="color: #103C7C">Monitoring {{ __('Department') }}</h5>

                        <div class="d-flex justify-content-end align-items-center" style="gap: 0.75rem">
                            <div class="d-flex align-items-center">
                                <label for="refreshInterval" class="mr-3 mb-0" style="white-space: nowrap;">Refresh / {{ __('Minutes') }}</label>
                                <select class="form-control" autocomplete="off" id="refreshInterval" style="max-width: 70px">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                </select>
                            </div>
    
                            <div class="px-2 py-2 rounded bg-warning text-white" style="font-size: .875rem;">
                                <span class="fas fa-redo"></span>
                                <span class="ml-2" id="refreshedAt">00:00:00</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-4" id="table">
                            <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('Service Name') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Waiting') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('Served') }}</th>
                                    <th rowspan="2" class="align-middle text-right">{{ __('No Show') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Waiting Time') }}</th>
                                    <th colspan="3" class="text-center">{{ __('Service Time') }}</th>
                                </tr>
    
                                <tr>
                                    {{-- Waktu Tunggu Child Header --}}
                                    <th class="text-center">{{ __('Currently') }}</th>
                                    <th class="text-center">{{ __('Average') }}</th>
                                    <th class="text-center">{{ __('Longest') }}</th>
    
                                    {{-- Waktu Melayani Child Header --}}
                                    <th class="text-center">{{ __('Currently') }}</th>
                                    <th class="text-center">{{ __('Average') }}</th>
                                    <th class="text-center">{{ __('Longest') }}</th>
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
<style>
    #table {
    border: 1px solid #33A0FF4D; 
    }

#table th,
#table td {
    border: 1px solid #33A0FF4D !important; 
}
</style>
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
                <td colspan="10" class="text-center">{{ __('Data not Found') }}</td>
            </tr>`)
            return
        }
        
        // Append response to table UI
        $("#table tbody").html(
            transformResponse(res)
        )
    }

    function transformResponse(data) {
        var maxwait = "{{ route('admin-branch.monitoring.department.maxwait', ':id') }}";
        var maxservice = "{{ route('admin-branch.monitoring.department.maxservice', ':id') }}";
        return data.map(v => {
            return `<tr style="color: #000">
                <td>${v.name}</td>
                <td class="text-right">${v.total_waiting}</td>
                <td class="text-right">${v.total_served}</td>
                <td class="text-right">${v.total_no_show}</td>
                <td class="text-center">${formatTime(v.now_waiting_duration)}</td>
                <td class="text-center">${formatTime(v.avg_waiting_duration)}</td>
                <td class="text-center"><a href="${maxwait.replace(':id', v.id)}">${formatTime(v.max_waiting_duration)}</a></td>
                <td class="text-center">${formatTime(v.now_served_duration)}</td>
                <td class="text-center">${formatTime(v.avg_served_duration)}</td>
                <td class="text-center"><a href="${maxservice.replace(':id', v.id)}">${formatTime(v.max_served_duration)}</a></td>

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
