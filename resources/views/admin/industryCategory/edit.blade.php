@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('edit.module', ['module' => __('Industry Category')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('admin.industryCategory.update', $category->id)}}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{$category->id}}">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                    value="{{old('name') ?: $category->name}}" required>
                                @include('layouts.inputError', ['errorName' => 'name'])
                            </div>

                            <div class="form-group">
                                <label for="icon">{{ __('Icon') }}</label>
                                <br>
                                <img src="{{asset('storage/'.$category->icon)}}" alt="" style="max-height: 100px">
                                <input name="icon" type="file" class="form-control @error('icon') is-invalid @enderror">
                                @include('layouts.inputError', ['errorName' => 'icon'])
                            </div>

                            <div class="form-group">
                                <label for="is_active">{{ __('Show in Mobile') }}</label>
                                <select name="is_active" id="is_active" class="form-control" required>
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                                @include('layouts.inputError', ['errorName' => 'is_active'])
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
            const is_activeOldValue = '{{ old('is_active') ?: $category->is_active }}';
            
            if(is_activeOldValue !== '') {
                $('#is_active').val(is_activeOldValue);
            }
        });
</script>
@endpush