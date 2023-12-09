@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('edit.module', ['module' => __('Service')]) }} {{$service->name}}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.service.update', $service->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $service->name}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>
                                <div class="form-group">
                                    <label for="department_id">{{ __('Department') }}</label>
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror">
                                        <option value="">{{ __('- Select Department -') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'department_id'])
                                </div>
                                @if(auth()->user()->Branch->BranchType->is_appointment)
                                    <div class="form-group">
                                        <label for="service_category_id">{{ __('Service Category') }}</label>
                                        <select name="service_category_id" id="service_category_id" class="form-control @error('service_category_id') is-invalid @enderror">
                                            <option value="">- {{ __('Select Service Category') }} -</option>
                                            @foreach ($service_categories as $service_category)
                                                <option value="{{$service_category->id}}" {{ $service->service_category_id == $service_category->id ? 'selected' : '' }}>{{$service_category->name}}</option>
                                            @endforeach
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'service_category_id'])
                                    </div>
                                @endif
                                @if ($isAllowConfigPrefix)
                                    <div class="form-group">
                                        <label for="prefix_queue">{{ __('Custom Prefix Number Queue') }}</label>
                                        <select name="prefix_queue" id="prefix_queue" class="form-control @error('prefix_queue') is-invalid @enderror">
                                            <option value=""> {{ __('Select Prefix') }} </option>
                                            @foreach ($prefixQueueList as $prefix)
                                                <option value="{{$prefix}}">{{$prefix}} xxx</option>
                                            @endforeach
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'prefix_queue'])
                                    </div>
                                @endif
                                @if ($isDirectQueueAndPemiumUser)
                                    <div class="form-group">
                                        <label for="prefix_queue">{{ __('SLA Duration Service') }}</label>
                                        <input name="sla_duration" type="number" class="form-control @error('sla_duration') is-invalid @enderror" value="{{old('sla_duration') ?: $service->sla_duration}}" min="0" required>
                                        <label style="font-size: 12px;">Satuan durasi dalam menit, untuk menonaktifkan isi angka 0</label>
                                        @include('layouts.inputError', ['errorName' => 'prefix_queue'])
                                    </div>
                                @endif
                                <div class="form-check form-group">
                                    <input name="is_show" id="is_show" type="checkbox" value="1" class="form-check-input @error('is_show') is-invalid @enderror"
                                            {{
                                                ($service && $service->is_show) || old('service')
                                                ? 'checked'
                                                : ''
                                            }}
                                        >
                                    <label for="is_show">{{ __('Show service to client') }}</label>
                                    @include('layouts.inputError', ['errorName' => 'is_show'])
                                </div>
                                <div class="form-check form-group">
                                    <input name="is_show_webkiosk" id="is_show_webkiosk" type="checkbox" value="1" class="form-check-input @error('is_show_webkiosk') is-invalid @enderror"
                                            {{
                                                ($service && $service->is_show_webkiosk) || old('service')
                                                ? 'checked'
                                                : ''
                                            }}
                                        >
                                    <label for="is_show_webkiosk">{{ __('Show service on webkiosk') }}</label>
                                    @include('layouts.inputError', ['errorName' => 'is_show_webkiosk'])
                                </div>
                                <button class="btn btn-warning">{{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            const prefix_queueOldValue = '{{ old('prefix_queue') ?: $service->prefix_queue }}';
            if(prefix_queueOldValue !== '') {
                $('#prefix_queue').val(prefix_queueOldValue.trim());
            }

            const department_idOldValue = '{{ old('department_id') ?: $service->department_id }}';

            if(department_idOldValue !== '') {
                $('#department_id').val(department_idOldValue);
            }
        });
    </script>
@endpush
