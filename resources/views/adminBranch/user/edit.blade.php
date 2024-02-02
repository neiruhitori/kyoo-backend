@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')

            <form action="{{route('admin-branch.branch-configuration.user.update', $user->id)}}" method="post">
                @csrf
                @method('PUT')

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ __('Change Password') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if ($user->is_password_changed)
                                    <div class="form-group">
                                        <label for="password_confirmation">{{ __('Old Password') }}</label>
                                        <input name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" value="{{old('old_password')}}" required>
                                        @include('layouts.inputError', ['errorName' => 'old_password'])
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="password">{{ __('New Password') }}</label>
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
                                    <label for="password_confirmation">{{ __('Confirm New Password') }}</label>
                                    <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}">
                                    @include('layouts.inputError', ['errorName' => 'password_confirmation'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-warning mb-4">{{ __('Update') }}</button>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            const workstation_idOldValue = '{{ old('workstation_id') ? $user->WorkstationVct ? $user->WorkstationVct->workstation_id : "" : "" }}';

            if(workstation_idOldValue !== '') {
                $('#workstation_id').val(workstation_idOldValue);
            }
        });
    </script>
@endpush
