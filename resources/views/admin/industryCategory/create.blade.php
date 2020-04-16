@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Insert Industry Category</h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>

                                <div class="form-group">
                                    <label for="icon">Icon</label>
                                    <input name="icon" type="file" class="form-control @error('icon') is-invalid @enderror" value="{{old('icon')}}" required>
                                    @include('layouts.inputError', ['errorName' => 'icon'])
                                </div>

                                <div class="form-group">
                                    <label for="name">Show on Mobile</label>
                                    <select name="" id="" class="form-control">
                                        <option value="">Yes</option>
                                        <option value="">No</option>
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'name'])
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