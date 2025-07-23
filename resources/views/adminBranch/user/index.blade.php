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
                                {{ __('infobox.virtualcounter1') }}
                            </li>
                            <li style="margin-bottom: 0.25rem;">
                                {{ __('infobox.virtualcounter2') }}                        
                            </li>
                            <li>
                                {{ __('infobox.virtualcounter3') }}                        
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
                
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold" style="color: #103C7C">
                                {{ __('list.module', ['module' => __('Staff')]) }}
                            </h5>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end" style="gap: 0.5rem;" >
                            <div class="d-flex align-items-center" id="searchBar">
                            </div>
                            @if (Auth::user()->Branch->BranchType->is_premium || count(Auth::user()->Branch->CS) < 1)
                                <div style="">
                                    <a href="{{ route('admin-branch.branch-configuration.user.create') }}" id="addStaffButton" class="btn btn-primary px-4" style="background-color: #103C7C">
                                        {{ __('create.module', ['module' => __('Staff')]) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-4" id="dataTable" width="100%" cellspacing="0">
                                    <thead style="background-color:#33A0FF4D; color: #103C7C;">
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Workstation') }}</th>
                                            <th>{{ __('Username') }}</th>
                                            <th>{{ __('Role') }}</th>
                                            @if(Auth::user()->Branch->BranchType->is_direct_queue)
                                                <th>{{ __('Group Level') }}</th>
                                            @endif
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>
                                                    {{
                                                        $user->WorkstationVct && $user->WorkstationVct->Workstation
                                                            ? $user->WorkstationVct->Workstation->name
                                                            : '-'
                                                    }}
                                                </td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ __('Counter') }}</td>
                                                @if(Auth::user()->Branch->BranchType->is_direct_queue)
                                                    <td>{{ $user->role == 'cs' ? 'Staff' : 'Supervisor' }}</td>
                                                @endif
                                                <td>
                                                    @if ($user->deleted_at)
                                                        <span class="badge badge-pill px-2 badge-danger">{{ __('Non Active') }}</span>
                                                    @else
                                                        <span class="badge badge-pill px-2 badge-primary">{{ __('Active') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$user->deleted_at)
                                                        <a
                                                            href="{{ route('admin-branch.branch-configuration.user.edit-workstation', $user->id) }}"
                                                            class="btn
                                                            btn-warning" data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('edit.module', ['module' => __('Staff')])
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        <a
                                                            href="{{ route('admin-branch.branch-configuration.user.edit', $user->id) }}"
                                                            class="btn
                                                            btn-warning" data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('Change Password')
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </a>
                                                        {{-- <form action="{{ route('admin-branch.branch-configuration.user.destroy', $user->id) }}" method="post" style="display: inline">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-danger"
                                                                data-toggle="tooltip"
                                                                data-placement="bottom"
                                                                title="{{
                                                                    __('remove.module', ['module' => __('Virtual Counter')])
                                                                }}"
                                                            >
                                                                <i class="fas fa-fw fa-trash"></i>
                                                            </button>
                                                        </form> --}}
                                                    @else
                                                        <form action="{{route('admin-branch.branch-configuration.user.restore')}}" method="post" style="display: inline">
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

                                                    <form action="{{route('admin-branch.branch-configuration.user.reset-password', $user->id)}}" method="post" style="display: inline">
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
@endsection
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

            });
             $('#dataTable_filter label').contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();
             $('#dataTable_filter label').addClass('d-flex align-items-center');
             $('#dataTable_filter label').appendTo('#searchBar');
             $('#dataTable_length label').addClass('d-flex align-items-center');
             $('#dataTable_length label').appendTo('#length');
             $('#dataTable_paginate').appendTo('#pagination');
    });
    </script>
@endpush
