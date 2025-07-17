@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
     <div class="accordion mb-3" id="accordionParent3">
        <div class="border-left-primary rounded " style="border-radius: 0.5rem; overflow: hidden;">

            <div  id="headingOne" style="background-color: #E6F3FF;">
                <button 
                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom" 
                    type="button"
                    data-toggle="collapse" 
                    data-target="#accordion3" 
                    aria-expanded="true" 
                    aria-controls="accordion3"
                    style="color: #103C7C; gap: 0.5rem; outline: none; box-shadow: none; padding: 1rem;"
                    >
                        <span class="fas fa-info-circle text-primary"></span>
                            <h5 class="font-weight-bold my-0 text-primary">
                                {{ __('Information') }}
                            </h5>
                </button>
            </div>

            <div 
                id="accordion3" 
                class="collapse show" 
                aria-labelledby="headingOne" 
                data-parent="#accordionParent3" 
                style="background-color: #E6F3FF;"
                >
                    <div style="padding: 0rem 1rem 1rem 1rem;">
                       <ul style="">
                            <li style="margin-bottom: 0.25rem;">
                               {{ __('infobox.workstation3') }}
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3"> --}}
                    {{-- </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold" style="color: #103C7C">{{ __('List Workstation Service') }}</h5>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end" style="gap: 0.5rem;" >
                            <div class="d-flex align-items-center" id="searchBar">
                            </div>
                            <a href="{{ route('admin-branch.branch-configuration.workstation.workstation-service.create', $workstation->id) }}" class="btn btn-primary" style="background-color: #103C7C">
                                {{ __('create.module', ['module' => __('Service')]) }}
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
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
                     <div class="row">
                        <div class="col-md-6 d-flex" id="length">
                        </div>
                        <div class="col-md-6 d-flex justify-content-end" id="pagination">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <style>
    table {
    border: 1px solid #33A0FF4D; 
    }

    table th,
    table td {

        border: 1px solid #33A0FF4D !important;
        text-align: center;
    }
    table td {
        color: black
    }

        .accordion-toggle-custom {
            transition: padding 0.3s ease;
        }

        .accordion-toggle-custom::after {
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                transition: transform 0.2s ease;
                margin-left: auto;
            }

        .accordion-toggle-custom[aria-expanded="false"]::after {
                    content: "\f107";
                }

        .accordion-toggle-custom[aria-expanded="true"]::after {
                    content: "\f106";
                }
</style>
@endsection

@push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
        $('#dataTable').dataTable({
            "ordering": false,
            "dom": 'Bfrtip',
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
                }, searchPlaceholder: "{{ __('Search') }}"
            },info: false,
        })
        $('#dataTable_filter label').contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();
             $('#dataTable_filter label').addClass('d-flex align-items-center m-0');
             $('#dataTable_filter label').appendTo('#searchBar');
             $('#dataTable_length label').addClass('d-flex align-items-center');
             $('#dataTable_length label').appendTo('#length');
             $('#dataTable_paginate').appendTo('#pagination');
    </script>
@endpush