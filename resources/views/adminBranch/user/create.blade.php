@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create CS Account</h6>
                </div>
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <form action="{{route('adminBranch.user.store')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="workstation_id">Workstation</label>
                                    <select name="workstation_id" id="workstation_id" class="form-control @error('workstation_id') is-invalid @enderror">
                                        <option value="">- Select Workstation -</option>
                                        @foreach ($workstations as $workstation)
                                            <option value="{{$workstation->id}}">{{$workstation->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'department_id'])
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">KY{{Auth::user()->branch_id}}_</span>
                                        </div>
                                        <input name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{old('username')}}" required>
                                    </div>
                                    @include('layouts.inputError', ['errorName' => 'username'])
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <br>
                                    <small>
                                        rules:
                                        <ul>
                                            <li>must be at least 8 characters in length</li>
                                            <li>must contain at least one lowercase letter</li>
                                            <li>must contain at least one uppercase letter</li>
                                            <li>must contain at least one digit</li>
                                        </ul>
                                    </small>
                                    <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'password'])
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmation Password</label>
                                    <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'password_confirmation'])
                                </div>
                                <button class="btn btn-primary">Save</button>
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
            const workstation_idOldValue = '{{ old('workstation_id') }}';
            
            if(workstation_idOldValue !== '') {
                $('#workstation_id').val(workstation_idOldValue);
            }
        });
    </script>
@endpush