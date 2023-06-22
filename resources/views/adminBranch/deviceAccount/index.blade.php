@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('list.module', ['module' => __('Device Account')]) }}
                    </h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    @if (Auth::user()->Branch->BranchType->is_premium)
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{route('admin-branch.branch-configuration.device-account.create')}}" class="btn btn-primary"">
                                    {{ __('create.module', ['module' => __('Device Account')]) }}
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
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Username') }}</th>
                                            <th>{{ __('Role') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ __('Device') }}</td>
                                                <td>
                                                    @if ($user->deleted_at)
                                                        <span class="badge badge-danger">{{ __('Non Active') }}</span>
                                                    @else
                                                        <span class="badge badge-primary">{{ __('Active') }}</span>                                                    
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$user->deleted_at)
                                                        <a
                                                            href="{{ route('admin-branch.branch-configuration.device-account.edit', $user->id) }}"
                                                            class="btn btn-warning" 
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('edit.module', ['module' => __('Device Account')])
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin-branch.branch-configuration.device-account.destroy', $user->id) }}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{
                                                                    __('remove.module', ['module' => __('Device Account')])
                                                                }}"
                                                            >
                                                                <i class="fas fa-fw fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{route('admin-branch.branch-configuration.device-account.restore')}}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="user_id" value="{{$user->id}}">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-primary"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{ __('Restore User') }}"
                                                            >
                                                                <i class="fas fa-fw fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{route('admin-branch.branch-configuration.device-account.reset-password', $user->id)}}" method="post" style="display: inline">
                                                        @csrf   
                                                        <button
                                                            type="submit"
                                                            class="btn btn-info"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{  __('Reset Password') }}"
                                                        >
                                                            <i class="fas fa-fw fa-lock"></i>
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