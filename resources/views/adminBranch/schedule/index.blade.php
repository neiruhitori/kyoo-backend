@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    Informasi
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    Tampilkan
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                    Jadwal hari kerja merupakan jadwal buka tutup kantor cabang Anda setiap hari. Anda juga dapat menambahkan informasi hari pengecualian dimana kantor cabang anda tutup karena mengikuti hari Libur Nasional.
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Working Days Schedule') }}</h6>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('admin-branch.branch-configuration.schedule.create')}}" class="btn btn-primary">
                                {{ __('create.module', ['module' => __('Schedule')]) }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-right">{{ __('No.') }}</th>
                                            <th>{{ __('Day') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="text-center">{{ __('Start Time') }}</th>
                                            <th class="text-center">{{ __('End Time') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $index => $schedule)
                                            <tr>
                                                <td class="text-right">{{ ++$index }}</td>
                                                <td>{{ __(ucfirst($schedule->day)) }}</td>
                                                <td>
                                                    @switch($schedule->status)
                                                        @case('closed')
                                                            <span class="badge badge-danger">{{ __('Closed') }}</span>
                                                            @break
                                                        @case('fullday')
                                                            <span class="badge badge-success">{{ __('Fullday') }}</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-primary">{{ __('Open') }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="text-center">{{$schedule->start_time}}</td>
                                                <td class="text-center">{{$schedule->end_time}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin-branch.branch-configuration.schedule.edit', $schedule->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="{{
                                                        __('edit.module', ['module' => __('Schedule')])
                                                    }}">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('admin-branch.branch-configuration.schedule.destroy', $schedule->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="{{
                                                            __('remove.module', ['module' => __('Schedule')])
                                                        }}">
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
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Holiday') }}</h6>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <a
                                    href="{{ route('admin-branch.branch-configuration.holiday.create') }}"
                                    class="btn btn-primary"
                                >
                                   {{ __('create.module',['module' => __('Holiday')])}}
                                </a>

                                <a
                                    href="{{ route('admin-branch.branch-configuration.holiday.template.create') }}"
                                    class="btn btn-primary"
                                >
                                    {{ __('Select Holiday Template') }}
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">{{ ('Date') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($holidays as $holiday)
                                            <tr>
                                                <td class="text-center">{{ $holiday->date }}</td>
                                                <td>{{ $holiday->name }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin-branch.branch-configuration.holiday.destroy', $holiday->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf

                                                        <button type="submit" class="btn btn-danger">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">{{ __('Data not Found') }}</td>
                                            </tr>
                                        @endforelse
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