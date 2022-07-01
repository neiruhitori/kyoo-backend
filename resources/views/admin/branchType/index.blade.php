@extends('layouts.app')

@push('css')
    <link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('list.module', ['module' => __('Branch Type')]) }}
                </h6>
            </div>

            <div class="card-body">
                @include('layouts.alert')

                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{route('admin.branchType.create')}}" class="btn btn-primary">
                            {{ __('create.module', ['module' => __('Branch Type')]) }}
                        </a>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Is Premium') }}</th>
                                        <th>Jenis Lisensi</th>
                                        <th>{{ __('Is Appointment Queue') }}</th>
                                        <th>{{ __('Is Direct Queue') }}</th>
                                        <th>{{ __('Is Exhibition Queue') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach ($branchTypes as $branchType)
                                        <tr>
                                            <td>{{$branchType->code}}</td>
                                            <td>{{$branchType->name}}</td>
                                            <td>
                                                @if ($branchType->is_premium)
                                                    <span class="badge badge-primary">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>

                                            <td>{{ $branchType->LicenseType->name }}</td>
                                            
                                            <td>
                                                @if ($branchType->is_appointment)
                                                    <span class="badge badge-primary">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                @if ($branchType->is_direct_queue)
                                                    <span class="badge badge-primary">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($branchType->is_exhibition)
                                                    <span class="badge badge-primary">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('No') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a
                                                    href="{{route('admin.branchType.edit', $branchType->id)}}"
                                                    class="btn btn-warning"
                                                    data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="{{
                                                        __('edit.module', [
                                                            'module' => __('Branch Type')
                                                        ])
                                                    }}"
                                                >
                                                    <i class="fas fa-fw fa-edit"></i>
                                                </a>

                                                <form
                                                    action="{{route('admin.branchType.destroy', $branchType->id)}}"
                                                    method="POST"
                                                    style="display: inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom" title="{{
                                                            __('remove.module', [
                                                                'module' => __('Branch Type')
                                                            ])
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
