@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List need to Verify</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Address</th>
                                            <th>Admin Contact</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branches as $branch)
                                            <tr>
                                                <td>{{$branch->id}}</td>
                                                <td>{{$branch->name}}</td>
                                                <td>{{$branch->IndustryCategory->name}}</td>
                                                <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                                <td>
                                                    <ul>
                                                        <li>Name: <b>{{$branch->Admin[0]->name}}</b></li>
                                                        <li>Email: <b>{{$branch->Admin[0]->email}}</b></li>
                                                        <li>Phone: <b>{{$branch->Admin[0]->phone}}</b></li>
                                                    </ul>
                                                </td>
                                                <td>{{$branch->created_at->format('D, d M Y')}}</td>
                                                <td>
                                                    @if ($branch->status == 'unverified')
                                                        <span class="badge badge-secondary">Unverified</span>
                                                    @else
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.branch.show', $branch->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Show Branch">
                                                        <i class="fas fa-fw fa-eye"></i>
                                                    </a>
                                                    <form action="{{route('admin.branch.verify.update', $branch->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="verified">
                                                        <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="bottom" title="Approve Branch">
                                                            <i class="fas fa-fw fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{route('admin.branch.verify.update', $branch->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Reject Branch">
                                                            <i class="fas fa-fw fa-times"></i>
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