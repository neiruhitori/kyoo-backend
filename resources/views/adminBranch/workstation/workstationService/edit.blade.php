@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Workstation Service</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('adminBranch.workstation.workstationService.update', ["workstation" => $workstationService->workstation_id, "workstationService" => $workstationService->id])}}" method="post">
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="workstation_id" value="{{$workstationService->workstation_id}}">
                                <div class="form-group">
                                    <label for="service_id">Service</label>
                                    <select name="service_id" id="service_id" class="form-control @error('service_id') is-invalid @enderror">
                                        <option value="">- Select Service -</option>
                                        @foreach ($services as $service)
                                            <option value="{{$service->id}}">{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'service_id'])
                                </div>
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror">
                                        <option value="">- Select Priority -</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'priority'])
                                </div>
                                <button class="btn btn-warning">Update</button>
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