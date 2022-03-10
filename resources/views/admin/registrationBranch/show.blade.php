@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Detail Branch') }}</h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <h5>{{$branch->name}}</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th colspan="2">
                                        <h5>{{ __('Profile') }}</h5>
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ __('Category') }}</th>
                                    <td>{{$branch->IndustryCategory->name}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Queue Type') }}</th>
                                    <td>
                                        @if ($branch->queue_type == 'direct_queue')
                                        <span class="badge badge-primary">{{ __('Direct Queue') }}</span>
                                        @else
                                        <span class="badge badge-info">{{ __('Appointment Queue') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <td>{{$branch->email}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Country') }}</th>
                                    <td>{{$branch->country}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <td>{{$branch->phone}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Address') }}</th>
                                    <td>{{$branch->address}}, {{$branch->Regency->name}}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Verified Email At') }}</th>
                                    <td>
                                        {{
                                            $branch->is_email_verified ? $branch->updated_at->format('D, d-m-Y H:i') : ''
                                        }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{route('admin.registrationBranch.update', $branch->id)}}" method="post"
                            style="display: inline">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-primary">{{ __('Approve') }}</button>
                        </form>
                        <form action="{{route('admin.registrationBranch.destroy', $branch->id)}}" method="post"
                            style="display: inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ml-5">{{ __('Reject') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection