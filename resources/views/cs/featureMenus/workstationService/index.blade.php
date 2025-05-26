@extends('layouts.appCS')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="card mb-4 custom-info" data-open="open" role="alert">
        <div class="card-body">
            <div class="custom-info-head">
                <h6 class="font-weight-bold my-0">
                    <span class="fas fa-info-circle text-primary mr-1"></span>
                    {{ __('Information') }}
                </h6>

                <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                    {{ __('Show') }}
                </button>
            </div>

            <div class="custom-info-body">
                <p>
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('infobox.workstation3') }}
                    </li>
                </ul>
                </p>
                <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
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
                            <a href="{{ route('cs.feature-menus.workstation-service.create') }}"
                               class="btn btn-primary">
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
                                                        ? $workstationService->Service['name']
                                                        : ''
                                                }}</td>
                                            <td>{{$workstationService->priority}}</td>
                                            <td>
                                                <a
                                                    href="{{ route('cs.feature-menus.workstation-service.edit', ["workstation_service" => $workstationService->id]) }}"
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
                                                    action="{{ route('cs.feature-menus.workstation-service.destroy', ["workstation_service" => $workstationService->id]) }}"
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

    <script>
        $(function() {
            $("[data-toggle=\"tooltip\"]").tooltip();
        });
        $('#dataTable').dataTable({
            "ordering": false,
            "language": {
               "emptyTable": "{{ __('No Data') }}",
                "info": "{{ __('Displaying :start to :end of :total data') }}"
                            .replace(':start', '_START_')
                            .replace(':end', '_END_')
                            .replace(':total', '_TOTAL_'),
                "infoEmpty": "{{ __('No Data') }}",
                "infoFiltered": "{{ __('(filtered from :max total data)') }}".replace(':max','_MAX_'),
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "{{ __('Show :menu data') }}".replace(':menu', '_MENU_'),
                "loadingRecords": "{{ __('Loading...') }}",
                "processing": "{{ __('Processing...') }}",
                "search": "{{ __('Search:') }}",
                "zeroRecords": "{{ __('No data found') }}",
                "paginate": {
                    "first": "{{ __('First') }}",
                        "last": "{{ __('Last') }}",
                        "next": "{{ __('Next') }}",
                        "previous": "{{ __('Previous') }}"
                },
                "aria": {
                    "sortAscending": "{{ __(': enable to sort the column in ascending order') }}",
                    "sortDescending": "{{ __(': enable to sort the column in descending order') }}"
                }
            }
        })
    </script>
@endpush
