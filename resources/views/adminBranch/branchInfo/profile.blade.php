@extends('layouts.app')

@push('css')
    <style>
        .custom-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-card-header h6 {
            color: black !important;
        }
    </style>
@endpush

@section('content')
<div class="card mb-4 custom-info" data-open="open" role="alert">
    <div class="card-body">
        <div class="custom-info-head">
            <h6 class="font-weight-bold my-0">
                <span class="fas fa-info-circle text-primary mr-1"></span>
                Informasi
            </h6>

            <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                Tampilkan
            </button>
        </div>

        <div class="custom-info-body">
            <p>
                Informasi tampilan cabang akan terhubung dengan tampilan informasi di Mobile Apps dan Web Antrian.
            </p>
            <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 custom-card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('Profile') }}
        </h6>

        <button class="btn btn-outline-primary" onclick="onEdit(this)">
            Edit
        </button>
    </div>

    <div class="card-body">
        @include('layouts.alert')

        <form
            action="{{route('adminBranch.branch.update')}}"
            method="post"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{$branch->id}}">

            <div class="form-group">
                <label for="name">{{ __('name.module', ['module' => __('Branch')]) }}</label>
                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $branch->name}}" readonly>
                @include('layouts.inputError', ['errorName' => 'name'])
            </div>

            <div class="form-group">
                <label for="industry_category_id">{{ __('Category') }}</label>
                <select name="industry_category_id" id="industry_category_id" class="form-control @error('industry_category_id') is-invalid @enderror" disabled>
                    @foreach ($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
                @include('layouts.inputError', ['errorName' => 'industry_category_id'])
            </div>

            <div class="form-group">
                <label for="branch_type_id">{{ __('Branch License') }}</label>
                <div class="font-weight-bold">{{ $branch->BranchType->code }} - {{ $branch->BranchType->name }}</div>
            </div>

            <div class="form-group">
                <label for="max_counter">{{ __('Max Counter') }}</label>
                
                <div class="font-weight-bold">{{ $branch->max_counter }}</div>
            </div>

            <div class="form-group">
                <label for="description">{{ __('Description') }}</label>
                <textarea name="description" id="" cols="" rows="" class="form-control @error('description') is-invalid @enderror" readonly>{{old('description') ?: $branch->description}}</textarea>
                @include('layouts.inputError', ['errorName' => 'description'])
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email') ?: $branch->email}}" readonly>
                @include('layouts.inputError', ['errorName' => 'email'])
            </div>

            <div class="form-group">
                <label for="country">{{ __('Country') }}</label>
                <select name="country" id="country" class="form-control @error('country') is-invalid @enderror" disabled>
                    @foreach ($countries as $country)
                        <option value="{{$country}}">{{$country}}</option>
                    @endforeach
                </select>
                @include('layouts.inputError', ['errorName' => 'country'])
            </div>

            <div class="form-group">
                <label for="timezone">{{ __('Indonesia Timezone') }}</label>
                <select name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror" disabled>
                    <option value="" selected disabled>{{ __('Select Timezone') }}</option>
                    <option value="WIB">{{ __('WIB') }}</option>
                    <option value="WITA">{{ __('WITA') }}</option>
                    <option value="WIT">{{ __('WIT') }}</option>
                </select>
                @include('layouts.inputError', ['errorName' => 'country'])
            </div>

            <div class="form-group">
                <label for="fixed_phone">{{ __('Fixed Phone') }}</label>
                <input name="fixed_phone" type="text" class="form-control @error('fixed_phone') is-invalid @enderror" value="{{old('fixed_phone') ?: $branch->fixed_phone}}" readonly>
                @include('layouts.inputError', ['errorName' => 'fixed_phone'])
            </div>

            <div class="form-group">
                <label for="mobile_phone">{{ __('Mobile Phone') }}</label>
                <input name="mobile_phone" type="text" class="form-control @error('mobile_phone') is-invalid @enderror" value="{{old('mobile_phone') ?: $branch->mobile_phone}}" readonly>
                @include('layouts.inputError', ['errorName' => 'mobile_phone'])
            </div>

            <div class="form-group">
                <label for="logo">{{ __('Logo') }}</label>
                <br>
                <img src="{{asset('storage/'.$branch->logo)}}" alt="" style="max-height: 100px">
                <br>
                <input name="logo" type="file" class="form-control d-none @error('logo') is-invalid @enderror" disabled>
                @include('layouts.inputError', ['errorName' => 'logo'])
            </div>

            <div class="form-group">
                <label for="photo">{{ __('Image Background') }}</label>
                <br>
                <img src="{{asset('storage/'.$branch->photo)}}" alt="" style="max-height: 100px">
                <br>
                <input name="photo" type="file" class="form-control d-none @error('photo') is-invalid @enderror" disabled>
                @include('layouts.inputError', ['errorName' => 'photo'])
            </div>

            <div class="form-group">
                <label for="is_active">{{ __('Show in Mobile') }}</label>
                <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror" disabled>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                </select>
                @include('layouts.inputError', ['errorName' => 'is_active'])
            </div>

            <button type="submit" class="btn btn-warning fullwidth d-none mb-3">{{ __('Update') }}</button>
        </form>
    </div>
</div>

@push('js')
    <script>
        function onEdit(e) {
            const isEdit = !$(e).hasClass('btn-outline-danger')

            if (isEdit) {
                $(e).addClass('btn-outline-danger');
                $(e).text('Batal')
                $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', false);
                $('input[type="file"], select').prop('disabled', false);
                $(".d-none").addClass('d-block');
            } else {
                $(e).removeClass('btn-outline-danger');
                $(e).text('Edit')
                $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', true);
                $('input[type="file"], select').prop('disabled', true);
                $(".d-none").removeClass('d-block');
            }
        }

        $(document).ready(function() {
            const industry_category_idOldValue = '{{ old('industry_category_id') ?: $branch->industry_category_id }}';
            
            if(industry_category_idOldValue !== '') {
                $('#industry_category_id').val(industry_category_idOldValue);
            }

            const countryOldValue = '{{ old('country') ?: $branch->country }}';
            
            if(countryOldValue !== '') {
                $('#country').val(countryOldValue);
            }

            const is_activeOldValue = '{{ old('is_active') ?: $branch->is_active }}';
            
            if(is_activeOldValue !== '') {
                $('#is_active').val(is_activeOldValue);
            }

            const timezoneOldValue = '{{ old('timezone') ?: $branch->timezone }}';
            
            if(timezoneOldValue !== '') {
                $('#timezone').val(timezoneOldValue);
            }

            const branch_type_idOldValue = '{{ old('branch_type_id') ?: $branch->branch_type_id }}';
            
            if(branch_type_idOldValue !== '') {
                $('#branch_type_id').val(branch_type_idOldValue);
            }
        });
    </script>
@endpush
@endsection