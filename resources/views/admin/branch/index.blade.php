@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List Branch</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{route('admin.branch.create')}}" class="btn btn-primary"">
                                Insert Branch
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Category</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Branch License</th>
                                            <th>Show in Mobile</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branches as $branch)
                                            <tr>
                                                <td>{{$branch->id}}</td>
                                                <td>
                                                    @isset($branch->logo)
                                                        <img src="{{asset('storage/'.$branch->logo)}}" style="max-width: 200px">
                                                    @endisset    
                                                </td>
                                                <td>{{$branch->IndustryCategory->name}}</td>
                                                <td>{{$branch->name}}</td>
                                                <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                                <td>
                                                    {{$branch->BranchType->code}} - {{$branch->BranchType->name}}    
                                                </td>
                                                <td>
                                                    @if ($branch->is_active)
                                                        <span class="badge badge-primary">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Non Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.branch.show', $branch->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Show Branch">
                                                        <i class="fas fa-fw fa-eye"></i>
                                                    </a>
                                                    <a href="{{route('admin.branch.edit', $branch->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Edit Branch">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
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