@extends('layouts.app')

@push('css')
<style>
    .number-list {
        display: flex;
    }

    .number-list .number-indicator {
        font-weight: bold;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        line-height: 0px;
        height: 1.25rem;
        width: 1.25rem;
        margin: 4px;
        background-color: black;
        color: white;
        border-radius: 999999999px;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('How to Configure Queue') }}
                    </h6>
                </div>
 
                <div class="card-body">
                    <p class="mb-5">
                        {{ __('Here are the steps you can take as a branch admin to ensure you and your customers can use the KYOO Queue platform') }}
                    </p>
                    
                    <div class="mb-4">
                        <h5 class="font-weight-bold mb-4">{{ __('The Main Queue Configuration') }}</h5>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">1</span>

                            <p>
                                <strong>{{ __('Step :no', ['no' => 1]) }}</strong>, {{ __('You can change') }} {{ __('Information about your branch office in') }}<a href="{{ route('admin-branch.branch-information.profile') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Branch Profile') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">2</span>

                            <p>
                                <strong>{{ __('Step :no', ['no' => 2]) }} </strong>, {{ __('You can change') }}  {{ __('Information about your branch location in') }} <a href="{{ route('admin-branch.branch-information.location') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Branch Location') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">3</span>

                            <p>
                                <strong>{{ __('Step :no', ['no' => 3]) }} </strong>, {{ __('You can change') }} {{ __('the type and services name to customers at') }} <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Service Type') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">4</span>

                            <p>
                                <strong>{{ __('Step :no', ['no' => 4]) }} </strong>, {{ __('You can change') }} {{ __('Your service opening and closing hours at') }} <a href="{{ route('admin-branch.branch-configuration.schedule.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2"> {{ __('Service Schedule') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">5</span>

                            <p>
                                <strong>{{ __('Step :no', ['no' => 5]) }} </strong>, {{ __('You can change') }} {{ __('the access to Service Officers at') }}<a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Staff') }}</a>
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h5 class="font-weight-bold mb-4">{{ __('Optional Configuration') }}</h5>

                        <p>{{ __('You can also change') }}</p>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">1</span>

                            <p>
                                {{ __('Department name from') }} <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Department') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">2</span>

                            <p>
                                {{ __('Service name from') }} <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Department') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">3</span>

                            <p>
                                {{ __('Workstation name from') }} <a href="{{ route('admin-branch.branch-configuration.workstation.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Workstation') }}</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">4</span>

                            <p>
                                {{ __('Virtual Counter name from') }} <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">{{ __('Virtual Counter') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection