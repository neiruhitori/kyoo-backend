@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('create.module', ['module' => __('Branch Type')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.branchType.store')}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="code">{{ __('Code') }}</label>
                                <input name="code" type="text" class="form-control @error('code') is-invalid @enderror"
                                    value="{{old('code')}}" required>
                                @include('layouts.inputError', ['errorName' => 'code'])
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    value="{{old('name')}}" required>
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
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_appointment'])
                            </div>

                            <div class="form-group">
                                <label for="is_direct_queue">{{ __('Is Direct Queue') }}?</label>
                                <select name="is_direct_queue" id="" class="form-control" required>
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_direct_queue'])
                            </div>
                            <button class="btn btn-primary">{{ __('Save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection