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
                            Departement merupakan hierarki atau unit paling tinggi di antrian KYOO. Untuk versi gratis hanya akan tersedia 1 departmen saja. Dibawah Departemen akan terdapat beberapa jenis layanan.
                        </li>
                        <li>
                            Daftar layanan adalah jenis layanan yang Kantor Cabang anda berikan kepada pelanggan
                        </li>
                    </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
            </div>
        </div>
    </div>

    @include('layouts.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('list.module', ['module' => __('Department')]) }}
                    </h6>
                </div>
                <div class="card-body">
                    @if (Auth::user()->Branch->BranchType->is_premium || count($departments) < 1)
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{route('admin-branch.branch-configuration.department.create')}}" class="btn btn-primary">
                                    {{ __('create.module', ['module' => __('Department')]) }}
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
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departments as $department)
                                            <tr>
                                                <td>{{$department->name}}</td>
                                                <td>
                                                    <a
                                                        href="{{
                                                            route('admin-branch.branch-configuration.department.edit', $department->id)
                                                        }}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="{{
                                                            __('edit.module', ['module' => __('Department')])
                                                        }}"
                                                    >
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

            @include('adminBranch.service.index', ['services' => $services])
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