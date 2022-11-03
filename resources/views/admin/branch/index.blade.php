@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .img-size-constraint {
        width: auto;
        height: auto;
        max-height: 40px;
        max-width: 40px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('list.module', ['module' => __('Branch')]) }}
                </h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{route('admin.branch.create')}}" class="btn btn-primary">
                                {{ __('create.module', ['module' => __('Branch')]) }}
                            </a>
                        </div>
                    </div>
                    <div class=" row">
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th class="text-center">{{ __('Logo') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Address') }}</th>
                                                <th>{{ __('Branch License') }}</th>
                                                <th>{{ __('Show in Mobile') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($branches as $branch)
                                            <tr>
                                                <td>{{$branch->id}}</td>
                                                <td class="text-center">
                                                    @isset($branch->logo)
                                                    <img src="{{asset('storage/'.$branch->logo)}}" class="img-size-constraint">
                                                    @endisset
                                                </td>
                                                <td>{{$branch->IndustryCategory->name}}</td>
                                                <td>{{$branch->name}}</td>
                                                <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                                <td>
                                                    @if ($branch->BranchType)
                                                    {{$branch->BranchType->code}} - {{$branch->BranchType->name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($branch->is_active)
                                                    <span class="badge badge-primary">{{ __('Active') }}</span>
                                                    @else
                                                    <span class="badge badge-danger">{{ __('Non Active') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{route('admin.branch.show', $branch->id)}}"
                                                        class="btn btn-primary"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{ __('Show Branch') }}"
                                                    >
                                                        <i class="fas fa-fw fa-eye"></i>
                                                    </a>

                                                    <a
                                                        href="{{route('admin.branch.edit', $branch->id)}}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{ __('edit.module', ['module' => __('Branch')]) }}"
                                                    >
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>

                                                    <a
                                                        href="{{ route('admin.branch.license', $branch->id) }}"
                                                        class="btn btn-secondary"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Edit Lisensi"
                                                    >
                                                        <i class="fas fa-cog"></i>
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