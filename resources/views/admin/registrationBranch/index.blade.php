@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('List need to Verify') }}</h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Address') }}</th>
                                        <th>{{ __('Admin Contact') }}</th>
                                        <th>{{ __('Email Verified') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($branches as $branch)
                                    <tr>
                                        <td>{{$branch->id}}</td>
                                        <td>{{$branch->name}}</td>
                                        <td>{{$branch->IndustryCategory->name}}</td>
                                        <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                        <td>
                                            <ul>
                                                <li>{{ __('Email') }}: <b>{{$branch->email}}</b></li>
                                                <li>{{ __('Phone') }}: <b>{{$branch->phone}}</b></li>
                                            </ul>
                                        </td>
                                        <td>
                                            @if ($branch->is_email_verified)
                                            <span class="badge badge-primary">{{ __('Verified') }}</span>
                                            @else
                                            <span class="badge badge-danger">{{ __('Not Verified') }}</span>
                                            @endif
                                        </td>
                                        <td>{{$branch->created_at->format('D, d M Y')}}</td>
                                        <td>
                                            <a href="{{route('admin.registrationBranch.show', $branch->id)}}"
                                                class="btn btn-primary" data-toggle="tooltip" data-placement="bottom"
                                                title="{{ __('Show Branch') }}">
                                                <i class="fas fa-fw fa-eye"></i>
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