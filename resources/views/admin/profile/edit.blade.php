@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Profile Admin</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin.profile.update')}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: Auth::user()->name}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email') ?: Auth::user()->email}}" required disabled>
                                    @include('layouts.inputError', ['errorName' => 'email'])
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{old('phone') ?: Auth::user()->phone}}" required>
                                    @include('layouts.inputError', ['errorName' => 'phone'])
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
                                    <label for="password_confirmation">Password Confirmation</label>
                                    <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'password_confirmation'])
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