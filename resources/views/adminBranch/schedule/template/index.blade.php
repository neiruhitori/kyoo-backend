@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('list.module', ['module' => __('Schedule Template')]) }}</h6>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        @foreach ($schedules as $index => $schedule)
                            <div class="card">
                                <div class="card-header" id="heading{{$index}}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseCard{{$index}}" aria-expanded="true" aria-controls="collapseCard{{$index}}">
                                            {{$schedule->name}}
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseCard{{$index}}" class="collapse" aria-labelledby="heading{{$index}}" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <form action="{{route('admin-branch.branch-configuration.schedule.template.update')}}" method="post">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="schedule_template_id" value="{{$schedule->id}}">
                                            <button class="btn btn-primary mb-3">{{ __('Choose') }} {{$schedule->name}}</button>
                                        </form>
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('ID') }}</th>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($schedule->ScheduleTemplateDetail as $detail)
                                                    <tr>
                                                        <td>{{$detail->id}}</td>
                                                        <td>{{$detail->description}}</td>
                                                        <td>{{$detail->date}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{route('admin-branch.branch-configuration.schedule.index')}}" class="btn btn-primary mt-3">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush