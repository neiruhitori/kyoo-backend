@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('create.module', ['module' => __('Workstation Service')]) }}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="service_id">{{ __('Service') }}</label>
                                    <input class="form-control" type="text" value="{{ $service->name }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="priority">{{ __('Sub Service') }}</label>
                                    <select name="sub_service" id="sub_service" class="form-control @error('sub_service') is-invalid @enderror">
                                        @foreach ($pool as $p)
                                        <option value="{{ $p->id }}">{{  $p->name }} </option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'sub_service'])
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