@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('create.module', ['module' => __('Industry Category')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.industryCategory.store')}}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    value="{{old('name')}}" required>
                                @include('layouts.inputError', ['errorName' => 'name'])
                            </div>

                            <div class="form-group">
                                <label for="icon">{{ __('Icon') }}</label>
                                <input name="icon" type="file" class="form-control @error('icon') is-invalid @enderror"
                                    value="{{old('icon')}}" required>
                                @include('layouts.inputError', ['errorName' => 'icon'])
                            </div>

                            <div class="form-group">
                                <label for="is_active">{{ __('Show in Mobile') }}</label>
                                <select name="is_active" id="" class="form-control" required>
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_active'])
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