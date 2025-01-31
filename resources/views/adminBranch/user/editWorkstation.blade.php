@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.alert')

            <form action="{{route('admin-branch.branch-configuration.user.update-workstation', $user->id)}}" method="post">
                @csrf
                @method('PUT')

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ __('Workstation Setup for Officers') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="user_id" value="{{$user->id}}">

                                <div class="form-group">
                                    <label for="workstation_id">{{ __('Workstation') }}</label>
                                    <select name="workstation_id" id="workstation_id" class="form-control @error('workstation_id') is-invalid @enderror">
                                        <option value="">- {{ __('Select Workstation') }} -</option>
                                        @foreach ($workstations as $workstation)
                                            <option
                                                value="{{ $workstation->id }}"
                                                {{ $user->WorkstationVct && $user->WorkstationVct->Workstation->id == $workstation->id ? 'selected' : '' }}
                                            >
                                                {{ $workstation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'workstation_id'])
                                </div>
                                <div class="form-group">
                                    <label for="username">{{ __('Username') }}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">KY{{Auth::user()->branch_id}}_</span>
                                        </div>
                                        <input name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{!!old('username') ?: (count(explode("_", $user->username)) > 1 ? explode("_", $user->username)[1] : '')!!}" required>
                                    </div>
                                    @include('layouts.inputError', ['errorName' => 'username'])
                                </div>
                                @if(Auth::user()->Branch->BranchType->is_direct_queue)
                                    <div class="form-group">
                                        <label for="role">{{ __('Group Level') }}</label>
                                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                            <option value="">- {{ __('Select Group Level') }} -</option>
                                            <option value="cs" {{ $user->role == 'cs' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                                            <option value="spv" {{ $user->role == 'spv' ? 'selected' : '' }}>{{ __('Supervisor') }}</option>
                                        </select>
                                        @include('layouts.inputError', ['errorName' => 'workstation_id'])
                                    </div>
                                @endif
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
