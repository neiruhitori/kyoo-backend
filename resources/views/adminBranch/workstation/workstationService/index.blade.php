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
                    <ul style="padding-left: 2rem;">
                        <li style="margin-bottom: 0.25rem;">
                            1 Meja bisa melayani 1 atau lebih jenis Layanan dengan prioritas layanan yang bisa di-atur. Untuk versi gratis maksimal hanya 5 layanan dalam 1 meja saja.
                        </li>
                        <li>
                            Untuk versi Antrian berbayar, 1 atau lebih Meja bisa melayani 1 atau lebih Layanan dengan Prioritas masing-masing Layanan yang bisa diatur.
                        </li>
                    </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('List Workstation Service') }}</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('admin-branch.branch-configuration.workstation.workstation-service.create', $workstation->id) }}" class="btn btn-primary">
                                {{ __('create.module', ['module' => __('Service')]) }}
                            </a>
                        </div>
                    </div>

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
                                                <td>{{
                                                    $workstationService->Service
                                                        ? $workstationService->Service->name
                                                        : ''
                                                }}</td>
                                                <td>{{$workstationService->priority}}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin-branch.branch-configuration.workstation.workstation-service.edit', ["workstation" => $workstation->id, "workstation_service" => $workstationService->id]) }}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{
                                                            __('edit.module', ['module' => __('Workstation Service')])
                                                        }}"
                                                    >
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('admin-branch.branch-configuration.workstation.workstation-service.destroy', ["workstation" => $workstation->id, "workstation_service" => $workstationService->id]) }}"
                                                        method="post"
                                                        style="display: inline"
                                                    >
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