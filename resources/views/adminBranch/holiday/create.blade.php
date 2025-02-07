@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('Add Holiday') }}
                    </h6>
                </div>

                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.holiday.store')}}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}</label>

                                    <input type="date" name="date" id="date" class="form-control">

                                    @include('layouts.inputError', ['errorName' => 'date'])
                                </div>

                                <div class="form-group">
                                    <label for="name">{{ __('Holiday Name') }}</label>

                                    <input type="text" name="name" id="name" class="form-control">

                                    @include('layouts.inputError', ['errorName' => 'name'])
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