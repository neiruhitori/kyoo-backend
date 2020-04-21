@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List Schedule Template {{$schedule->name}}</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <form action="{{route('admin.scheduleTemplate.update', $schedule->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <label for="file">File Excel</label>
                                <input name="file" type="file" class="form-control" required>
                                @include('layouts.inputError', ['errorName' => 'file'])
                                <button class="btn btn-warning btn-sm mt-2">
                                    Update Template
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
                                            <th>Description</th>
                                            <th>Date</th>
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