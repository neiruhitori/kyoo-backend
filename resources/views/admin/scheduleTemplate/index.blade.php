@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List Industry Category</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <form action="{{route('admin.scheduleTemplate.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name">Template Name</label>
                                <input name="name" type="text" class="form-control" required>
                                @include('layouts.inputError', ['errorName' => 'name'])
                                <label for="file">File Excel</label>
                                <input name="file" type="file" class="form-control" required>
                                @include('layouts.inputError', ['errorName' => 'file'])
                                <button class="btn btn-primary btn-sm mt-2">
                                    Insert Template
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Template</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $schedule)
                                            <tr>
                                                <td>{{$schedule->id}}</td>
                                                <td>{{$schedule->ScheduleTemplate->name}}</td>
                                                <td>{{$schedule->description}}</td>
                                                <td>{{$schedule->date}}</td>
                                                <td>
                                                    <a href="{{route('admin.scheduleTemplateDetail.edit', $schedule->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Edit Category">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('admin.scheduleTemplateDetail.destroy', $schedule->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Remove Category">
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