@extends('layouts.app')

@section('content')
    @include('layouts.alert')

    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin-branch.branch-configuration.device-account.store')}}" method="post">
                @csrf

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Setting {{ __('Device Account') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="username">{{ __('Username') }}</label>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">
                                                DEV{{ Auth::user()->branch_id }}_
                                            </span>
                                        </div>

                                        <input
                                            name="username"
                                            type="text"
                                            class="form-control @error('username') is-invalid @enderror"
                                            value="{{ old('username') }}"
                                            required
                                        >
                                    </div>

                                    @include('layouts.inputError', ['errorName' => 'username'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ __('New Password') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <br>
                            <small>
                                {{ __('Rules') }}:
                                <ul>
                                    <li>{{ __('must be at least 8 characters in length') }}</li>
                                    <li>{{ __('must contain at least one lowercase letter') }}</li>
                                    <li>{{ __('must contain at least one uppercase letter') }}</li>
                                    <li>{{ __('must contain at least one digit') }}</li>
                                </ul>
                            </small>
                            <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}">
                            @include('layouts.inputError', ['errorName' => 'password'])
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">{{ __('Password Confirmation') }}</label>
                            <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}">
                            @include('layouts.inputError', ['errorName' => 'password_confirmation'])
                        </div>
                    </div>
                </div>

                <button class="btn btn-warning mb-4">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
@endsection