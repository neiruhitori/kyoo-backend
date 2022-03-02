@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('edit.module', ['module' => __('Branch Type')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.branchType.update', $branchType->id)}}" method="post">
                            @csrf
                            <input type="hidden" value="PUT" name="_method">
                            <input type="hidden" value="{{ $branchType->id }}" name="id">
                            <div class="form-group">
                                <label for="code">{{ __('Code') }}</label>
                                <input name="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                    value="{{old('code') ?: $branchType->code}}" required>
                                @include('layouts.inputError', ['errorName' => 'code'])
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    value="{{old('name') ?: $branchType->name}}" required>
                                @include('layouts.inputError', ['errorName' => 'name'])
                            </div>

                            <div class="form-group">
                                <label for="is_premium">{{ __('Is Premium') }}?</label>
                                <select name="is_premium" id="" class="form-control" required>
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_premium'])
                            </div>

                            <div class="form-group">
                                <label for="is_appointment">{{ __('Is Appointment Queue') }}?</label>
                                <select name="is_appointment" id="" class="form-control" required>
                                    <option value="0" {{ !$branchType->is_appointment ?: 'selected' }}>{{ __('No') }}</option>
                                    <option value="1" {{ !$branchType->is_appointment ?: 'selected' }}>{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_appointment'])
                            </div>

                            <div class="form-group">
                                <label for="is_direct_queue">{{ __('Is Direct Queue') }}?</label>
                                <select name="is_direct_queue" id="" class="form-control" required>
                                    <option value="0" {{ !$branchType->is_direct_queue ?: 'selected' }}>{{ __('No') }}</option>
                                    <option value="1" {{ !$branchType->is_direct_queue ?: 'selected' }}>{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_direct_queue'])
                            </div>

                            <div class="form-group">
                                <label for="is_exhibition">{{ __('Is Exhibition Queue') }}?</label>
                                <select name="is_exhibition" id="" class="form-control" required>
                                    <option value="0" {{ !$branchType->is_exhibition ?: 'selected' }}>{{ __('No') }}</option>
                                    <option value="1" {{ !$branchType->is_exhibition ?: 'selected' }}>{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_exhibition'])
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
            const is_premiumOldValue = '{{ old('is_premium') ?: $branchType->is_premium }}';
            
            if(is_premiumOldValue !== '') {
                $('#is_premium').val(is_premiumOldValue);
            }

            const is_appointmentOldValue = '{{ old('is_appointment') ?: $branchType->is_appointment }}';
            
            if(is_appointmentOldValue !== '') {
                $('#is_appointment').val(is_appointmentOldValue);
            }

            const is_direct_queueOldValue = '{{ old('is_direct_queue') ?: $branchType->is_direct_queue }}';
            
            if(is_direct_queueOldValue !== '') {
                $('#is_direct_queue').val(is_direct_queueOldValue);
            }
        });
</script>
@endpush