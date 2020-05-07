@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Working Days Schedule</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{route('adminBranch.schedule.create')}}" class="btn btn-primary"">
                                Insert Schedule
                            </a>
                        </div>
                    </div>
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Status</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $schedule)
                                            <tr>
                                                <td>{{$schedule->day}}</td>
                                                <td>
                                                    @switch($schedule->status)
                                                        @case('closed')
                                                            <span class="badge badge-danger">Closed</span>
                                                            @break
                                                        @case('fullday')
                                                            <span class="badge badge-success">Fullday</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-primary">Open</span>
                                                    @endswitch
                                                </td>
                                                <td>{{$schedule->start_time}}</td>
                                                <td>{{$schedule->end_time}}</td>
                                                <td>
                                                    <a href="{{route('adminBranch.schedule.edit', $schedule->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Edit Schedule">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('adminBranch.schedule.destroy', $schedule->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Remove Schedule">
                                                            <i class="fas fa-fw fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Public Holiday</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 alert alert-primary">
                            @if (Auth::user()->Branch->schedule_template_id)
                                <h5>Youre using {{Auth::user()->Branch->ScheduleTemplate->name}}</h5>
                            @endif
                            <a href="{{route('adminBranch.schedule.template.index')}}"><b>Click here to add public holidays schedule as exceptional working days</b></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (Auth::user()->Branch->schedule_template_id)
                                            @foreach (Auth::user()->Branch->ScheduleTemplate->ScheduleTemplateDetail as $schedule)
                                                <tr>
                                                    <td>{{$schedule->description}}</td>
                                                    <td>{{$schedule->date}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush