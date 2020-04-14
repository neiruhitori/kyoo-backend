@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Insert a New Branch</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Industry Category</label>
                                    <select name="" id="" class="form-control">
                                        <option value="">Healtcare</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'email'])
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Province</label>
                                            <select name="" id="" class="form-control">
                                                <option value="">Healtcare</option>
                                            </select>
                                            @include('layouts.inputError', ['errorName' => 'name'])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">City</label>
                                            <select name="" id="" class="form-control">
                                                <option value="">Healtcare</option>
                                            </select>
                                            @include('layouts.inputError', ['errorName' => 'name'])
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Address</label>
                                    <textarea name="" id="" cols="" rows="" class="form-control"></textarea>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Latitude</label>
                                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                            @include('layouts.inputError', ['errorName' => 'name'])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Longitude</label>
                                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                            @include('layouts.inputError', ['errorName' => 'name'])
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="name">Phone</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="icon">Logo</label>
                                            <input name="icon" type="file" class="form-control @error('icon') is-invalid @enderror" value="{{old('icon')}}" required>
                                            @include('layouts.inputError', ['errorName' => 'icon'])
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="icon">Background Photo</label>
                                            <input name="icon" type="file" class="form-control @error('icon') is-invalid @enderror" value="{{old('icon')}}" required>
                                            @include('layouts.inputError', ['errorName' => 'icon'])
                                        </div>
                                    </div>
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