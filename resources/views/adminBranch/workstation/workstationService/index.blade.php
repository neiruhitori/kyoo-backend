@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('List Workstation Service') }}</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    @if (Auth::user()->Branch->BranchType->is_premium || count($workstation->WorkstationService) < 1)
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{route('adminBranch.workstation.workstationService.create', $workstation->id)}}" class="btn btn-primary">
                                    {{ __('create.module', ['module' => __('Service')]) }}
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
                                            <th>{{ __('Service') }}</th>
                                            <th>{{ __('Priority') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workstation->WorkstationService as $workstationService)
                                            <tr>
                                                <td>{{$workstationService->Service->name}}</td>
                                                <td>{{$workstationService->priority}}</td>
                                                <td>
                                                    <a
                                                        href="{{route('adminBranch.workstation.workstationService.edit', ["workstation" => $workstation->id, "workstationService" => $workstationService->id])}}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{
                                                            __('edit.module', ['module' => __('Workstation Service')])
                                                        }}"
                                                    >
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('adminBranch.workstation.workstationService.destroy', ["workstation" => $workstation->id, "workstationService" => $workstationService->id])}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('edit.module', ['module' => __('Workstation Service')])
                                                            }}"
                                                        >
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