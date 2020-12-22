@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Virtual Counter List</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    @if (count(Auth::user()->Branch->CS) < 1)
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{route('adminBranch.user.create')}}" class="btn btn-primary"">
                                    Create User Counter
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Workstation</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Auth::user()->Branch->CS as $user)
                                            <tr>
                                                <td>{{$user->id}}</td>
                                                <td>{{$user->WorkstationVct && $user->WorkstationVct->Workstation ? $user->WorkstationVct->Workstation->name : '-'}}</td>
                                                <td>{{$user->username}}</td>
                                                <td>Counter</td>
                                                <td>
                                                    @if ($user->deleted_at)
                                                            <span class="badge badge-danger">Non Active</span>
                                                        @else
                                                            <span class="badge badge-primary">Active</span>                                                    
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$user->deleted_at)
                                                        <a href="{{route('adminBranch.user.edit', $user->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Edit User">
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        <form action="{{route('adminBranch.user.destroy', $user->id)}}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Remove User">
                                                                <i class="fas fa-fw fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @else
                                                        <form action="{{route('adminBranch.user.restore')}}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="user_id" value="{{$user->id}}">
                                                            <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Restore User">
                                                                <i class="fas fa-fw fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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