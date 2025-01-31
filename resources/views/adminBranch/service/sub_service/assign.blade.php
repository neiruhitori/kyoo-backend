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
                            1 Layanan bisa memiliki 1 atau lebih jenis Sub Layanan
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
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Sub-Service list in :serv', ['serv' => $service->name]) }}</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('admin-branch.branch-configuration.service.add.sub-service', $service->id) }}" class="btn btn-primary">
                                {{ __('create.module', ['module' => __('Sub Layanan')]) }}
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Service Name') }}</th>
                                            <th>{{ __('Sub Service') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($service->subServices && $service->subServices->isNotEmpty())
                                        @foreach ($service->subServices as $subService)
                                            <tr>
                                                <td>{{ $service->name ?? '' }}</td> 
                                                <td>{{ $subService->name ?? '' }}</td>
                                                <td>
                                                    <a href="{{ route('admin-branch.branch-configuration.service.edit.sub-service', $subService->pivot->id) }}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom"
                                                       title="{{ __('edit.module', ['module' => __('Sub Service')]) }}">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                
                                                    <form action="{{ route('admin-branch.branch-configuration.service.remove.sub-service', $subService->pivot->id) }}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom"
                                                                title="{{ __('remove.module', ['module' => __('Sub Service')]) }}">
                                                            <i class="fas fa-fw fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">{{ __('Sub Service Not Found') }}</td>
                                        </tr>
                                    @endif
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