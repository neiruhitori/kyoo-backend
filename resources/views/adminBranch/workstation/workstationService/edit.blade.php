@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')
            <div class="card shadow mb-4">
                {{-- <div class="card-header py-3"> --}}
                    {{-- </div> --}}
                @csrf
                <div class="card-body">
                    <h5 class="mb-4 font-weight-bold" style="color: #103c7c">
                        {{ __('edit.module', ['module' => __('Workstation Service')]) }}
                    </h5>
                    <form action="{{route('admin-branch.branch-configuration.workstation.workstation-service.update', ["workstation" => $workstationService->workstation_id, "workstation_service" => $workstationService->id])}}" method="post">
                        @csrf
                    <div class="row">
                        <div class="col-md-6">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="workstation_id" value="{{$workstationService->workstation_id}}">
                                <div class="form-group">
                                    <label for="service_id">{{ __('Service') }}</label>
                                    <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror">
                                        <option value="">{{ __('- Select Service -') }}</option>
                                        @foreach ($services as $service)
                                            <option value="{{$service->id}}">{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'service_id'])
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">{{ __('Priority') }}</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror">
                                        <option value="">{{ __('- Select Priority -') }}</option>
                                        <option value="1">{{ __('1') }}</option>
                                        <option value="2">{{ __('2') }}</option>
                                        <option value="3">{{ __('3') }}</option>
                                        <option value="4">{{ __('4') }}</option>
                                        <option value="5">{{ __('5') }}</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'priority'])
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <button class="btn btn-warning">{{ __('Update') }}</button>
                            </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            const service_idOldValue = '{{ old('service_id') ?: $workstationService->service_id }}';
            
            if(service_idOldValue !== '') {
                $('#service_id').val(service_idOldValue);
            }

            const priorityOldValue = '{{ old('priority') ?: $workstationService->priority }}';
            
            if(priorityOldValue !== '') {
                $('#priority').val(priorityOldValue);
            }
        });
    </script>
@endpush