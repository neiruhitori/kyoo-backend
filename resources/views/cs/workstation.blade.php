@extends('layouts.appCS')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('list.module', ['module' => __('Workstation')]) }}
                </h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Workstation List') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Username') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workstations as $workstation)
                                        <tr>
                                            <td>{{$workstation->name}}</td>
                                            <td>
                                                @if($workstation->vct_name)
                                                    <span class="badge badge-danger">Login</span>
                                                @else
                                                    <span class="badge badge-success">Logout</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($workstation->vct_id == Auth::id())
                                                    <span class="badge badge-primary">
                                                        {{ $workstation->vct_name }}
                                                    </span>
                                                @elseif($workstation->vct_name)
                                                    <span class="badge badge-secondary">
                                                        {{ $workstation->vct_name }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$workstation->vct_id)
                                                    <form action="{{route('cs.updateWorkstation', Auth::user()->id)}}" method="post" style="display: inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="workstation_id" value="{{ $workstation->id }}">
                                                        <button
                                                            type="submit"
                                                            class="btn btn-warning"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{
                                                                __('edit.module', ['module' => __('Workstation')])
                                                            }}"
                                                        >
                                                            <i class="fas fa-fw fa-edit"></i>
                                                        </button>
                                                    </form>
                                                @endif
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
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
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
