@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('create.module', ['module' => __('Sub Service')]) }}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.sub-service.store')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?? 'Sub Layanan 1'}}" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
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
@push('js')
    <script>
        $(document).ready(function() {
            const department_idOldValue = '{{ old('department_id') }}';

            if(department_idOldValue !== '') {
                $('#department_id').val(department_idOldValue);
            }
        });
    </script>
@endpush
