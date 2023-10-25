@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('edit.module', ['module' => __('Service Category')]) }} {{$service_category->name}}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.service-category.update', $service_category->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $service_category->name}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>
                                <button class="btn btn-warning">{{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
