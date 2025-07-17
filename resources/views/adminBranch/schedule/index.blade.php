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
            <div style="padding: 0rem 1rem 1rem 2.5rem;">
                <p class="mb-0">
                    {{ __('infobox.schedule') }} 
                </p>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')

            <div class="card shadow mb-4">
                
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold" style="color: #103C7C">{{ __('Working Days Schedule') }}</h5>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center" style="gap: 1rem">
                            <div class="d-flex align-items-center" id="searchBar">
                            </div>
                            <a href="{{route('admin-branch.branch-configuration.schedule.create')}}" class="btn btn-primary" style="background-color: #103C7C">
                                {{ __('create.module', ['module' => __('Schedule')]) }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-cust" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                        <tr>
                                            <th class="text-right">{{ __('No.') }}</th>
                                            <th>{{ __('Day') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th class="text-center">{{ __('Start Time') }}</th>
                                            <th class="text-center">{{ __('End Time') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schedules as $index => $schedule)
                                            <tr>
                                                <td class="text-right">{{ ++$index }}</td>
                                                <td>{{ __(ucfirst($schedule->day)) }}</td>
                                                <td>
                                                    @switch($schedule->status)
                                                        @case('closed')
                                                            <span class="badge badge-danger badge-pill px-2">{{ __('Closed') }}</span>
                                                            @break
                                                        @case('fullday')
                                                            <span class="badge badge-success badge-pill px-2">{{ __('Fullday') }}</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-primary badge-pill px-2">{{ __('Open') }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="text-center">{{$schedule->start_time}}</td>
                                                <td class="text-center">{{$schedule->end_time}}</td>
                                                <td class="text-center">
                                                    <a href="{{route('admin-branch.branch-configuration.schedule.edit', $schedule->id)}}" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="{{
                                                        __('edit.module', ['module' => __('Schedule')])
                                                    }}">
                                                        <i class="fas fa-fw fa-edit"></i>
                                                    </a>
                                                    <form action="{{route('admin-branch.branch-configuration.schedule.destroy', $schedule->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="{{
                                                            __('remove.module', ['module' => __('Schedule')])
                                                        }}">
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
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <h5 class="m-0 font-weight-bold" style="color: #103C7C">{{ __('Holiday') }}</h5>
                        </div>
                        <div class="col-md-6 text-right">
                                <a
                                    href="{{ route('admin-branch.branch-configuration.holiday.create') }}"
                                    class="btn btn-primary"
                                    style="background-color: #103C7C"
                                >
                                   {{ __('create.module',['module' => __('Holiday')])}}
                                </a>
    
                                <a
                                    href="{{ route('admin-branch.branch-configuration.holiday.template.create') }}"
                                    class="btn btn-primary"
                                    style="background-color: #103C7C"
                                >
                                    {{ __('Select Holiday Template') }}
                                </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-cust" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                        <tr>
                                            <th class="text-center">{{ ('Date') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($holidays as $holiday)
                                            <tr>
                                                <td class="text-center">{{ $holiday->date }}</td>
                                                <td>{{ $holiday->name }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('admin-branch.branch-configuration.holiday.destroy', $holiday->id) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf

                                                        <button type="submit" class="btn btn-danger">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">{{ __('Data not Found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .table-cust {
            border: 1px solid #33A0FF4D; 
        }

        .table-cust th,
        .table-cust td {
            border: 1px solid #33A0FF4D !important; 
        }
            .accordion-toggle-custom.collapsed {
                padding-bottom: 1rem !important;
            }
    
            .accordion-toggle-custom:not(.collapsed) {
                padding-bottom: 0rem !important;
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
    {{-- <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script> --}}

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
        $(document).ready(function() {
            $('#dataTable').dataTable({
                "language": {
                    "emptyTable": "{{ __('No Data') }}",
                    "info": "{{ __('Displaying :start to :end of :total data') }}"
                            .replace(':start', '_START_')
                            .replace(':end', '_END_')
                            .replace(':total', '_TOTAL_'),
                    "infoEmpty": "{{ __('No Data') }}",
                    "infoFiltered": "",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "{{ __('Show :menu data') }}"
                                    .replace(':menu', '_MENU_'),
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
                    searchPlaceholder: "{{ __('Search') }}"
                },
                info: false,
            })
            $('#dataTable_filter label').contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();
            $('#dataTable_filter label').addClass('m-0');
            $('#dataTable_filter label').appendTo('#searchBar');
            $('#dataTable_length label').addClass('d-flex align-items-center');
            $('#dataTable_length label').appendTo('#length');
            $('#dataTable_paginate').appendTo('#pagination');
        })
    </script>
@endpush