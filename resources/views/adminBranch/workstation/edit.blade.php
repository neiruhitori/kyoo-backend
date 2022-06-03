@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ __('edit.module', ['module' => __('Workstation')]) }} {{$workstation->name}}
                    </h6>
                </div>
                @csrf
                <div class="card-body">
                    @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('admin-branch.branch-configuration.workstation.update', $workstation->id)}}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="department_id">{{ __('Department') }}</label>
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach ($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('layouts.inputError', ['errorName' => 'department_id'])
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input name="name" id="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $workstation->name}}" onchange="onChangeName()" required>
                                    @include('layouts.inputError', ['errorName' => 'name'])
                                </div>
                                <div class="form-group">
                                    <label for="label">{{ __('Label') }}</label>
                                    <input name="label" type="text" class="form-control @error('label') is-invalid @enderror" value="{{old('label') ?: $workstation->label}}" required readonly>
                                    @include('layouts.inputError', ['errorName' => 'label'])
                                </div>
                                <div class="form-group">
                                    <label for="display_id">{{ __('Display ID') }}</label>
                                    <input name="display_id" id="display_id" type="text" class="form-control @error('display_id') is-invalid @enderror" value="{{old('display_id') ?: $workstation->display_id}}" required readonly>
                                    @include('layouts.inputError', ['errorName' => 'display_id'])
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
@push('js')
    <script>
        $(document).ready(function() {
            const department_idOldValue = '{{ old('department_id') ?: $workstation->department_id }}';
            
            if(department_idOldValue !== '') {
                $('#department_id').val(department_idOldValue);
            }
        });
        function onChangeName(){
            const name = $('#name').val()
            $('#display_id').val(name)
        }
    </script>
@endpush