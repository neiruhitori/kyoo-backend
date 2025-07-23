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
        .accordion-toggle-custom.collapsed {
            padding-bottom: 1rem !important;
        }

        .accordion-toggle-custom:not(.collapsed) {
            padding-bottom: 0rem !important;
        }

        .accordion-toggle-custom {
            transition: padding 0.3s ease;
        }

        .accordion-toggle-custom::after {
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                transition: transform 0.2s ease;
                margin-left: auto;
            }

        .accordion-toggle-custom[aria-expanded="false"]::after {
                    content: "\f107";
                }

        .accordion-toggle-custom[aria-expanded="true"]::after {
                    content: "\f106";
                }
    </style>
@endpush

@section('content')
                    <div class="accordion mb-3" id="accordionParent3">
                        <div class="border-left-primary rounded " style="border-radius: 0.5rem; overflow: hidden;">

                            <div  id="headingOne" style="background-color: #E6F3FF;">
                                <button 
                                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom" 
                                    type="button"
                                    data-toggle="collapse" 
                                    data-target="#accordion3" 
                                    aria-expanded="true" 
                                    aria-controls="accordion3"
                                    style="color: #103C7C; gap: 0.5rem; outline: none; box-shadow: none; padding: 1rem;"
                                >
                                    <span class="fas fa-info-circle text-primary"></span>
                                    <h5 class="font-weight-bold my-0 text-primary">
                                        {{ __('Information') }}
                                    </h5>
                                </button>
                            </div>

                            <div 
                                id="accordion3" 
                                class="collapse show" 
                                aria-labelledby="headingOne" 
                                data-parent="#accordionParent3" 
                                style="background-color: #E6F3FF;"
                            >
                                <div style="padding: 0rem 1rem 1rem 2.5rem;">
                                    <p class="mb-0">
                                        {{ __('infobox.profile') }}
                                    </p>
                            </div>
                        </div>
                        </div>
                    </div>

<div class="card shadow mb-4">
    <h5 class="p-3 font-weight-bold" style="color: #103C7C">
        {{ __('Profile') }}
    </h5>

    <div class="card-body">
        @include('layouts.alert')

        <form
            action="{{route('admin-branch.branch-information.update')}}"
            method="post"
            enctype="multipart/form-data"
            id="editFORM"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{$branch->id}}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="photo">{{ __('Image Background') }}</label>
                        <br>
                        <div class="d-flex align-items-center" style="gap: 2rem">
                            @if (isset($branch->photo))
                                <img src="{{asset('storage/'.$branch->photo)}}" 
                                id="imgOriginal1"
                                data-original="{{ asset('storage/' . $branch->photo) }}"
                                alt="" style="max-height: 100px">
                            @else
                                <img src="{{asset('img/img-placeholder.jpg')}}" alt="" style="max-height: 100px" id="imgPlaceholder1">
                            @endif
                            <img id="preview1" class="d-none" style="max-width: 100px;">
                            <div class="d-none flex-column custom-upload">
                                <label class="btn px-4" style="background-color: #103C7C; color:#fff; cursor: pointer;">
                                    Upload Image <input type="file" name="photo" hidden id="fileInput1" onchange="previewImage(this,1)" disabled>
                                </label>
                                <p id="fileName1" class="ml-2 text-muted text-center">JPG or PNG, 10MB Max</p>
                            </div>
                            {{-- <input name="photo" type="file" class="form-control  @error('photo') is-invalid @enderror" disabled> --}}
                        </div>
                        @include('layouts.inputError', ['errorName' => 'photo'])
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="logo">{{ __('Logo') }}</label>
                        <br>
                        <div class="d-flex align-items-center" style="gap: 2rem">
                            @if (isset($branch->logo))
                                <img src="{{asset('storage/'.$branch->logo)}}" 
                                id="imgOriginal2"
                                data-original="{{ asset('storage/' . $branch->logo) }}"
                                alt="" style="max-height: 100px">
                            @else
                                <img src="{{asset('img/img-placeholder.jpg')}}" alt="" style="max-height: 100px" id="imgPlaceholder2">
                            @endif
                            <img id="preview2" class="d-none" style="max-width: 100px;">
                                <div class="d-none flex-column custom-upload">
                                    <label class="btn px-4" style="background-color: #103C7C; color:#fff; cursor: pointer;">
                                        Upload Image <input type="file" name="logo" hidden id="fileInput2" onchange="previewImage(this,2)" disabled>
                                    </label>
                                    <p id="fileName2" class="ml-2 text-muted text-center">JPG or PNG, 10MB Max</p>
                                </div>
                        </div>
                        {{-- <input name="logo" type="file" class="form-control d-none mt-3 @error('logo') is-invalid @enderror" disabled> --}}
                        @include('layouts.inputError', ['errorName' => 'logo'])
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="name">{{ __('Branch Name') }}</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{old('name') ?: $branch->name}}" readonly>
                        @include('layouts.inputError', ['errorName' => 'name'])
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="industry_category_id">{{ __('Category') }}</label>
                        <select name="industry_category_id" id="industry_category_id" class="form-control @error('industry_category_id') is-invalid @enderror" disabled>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                        @include('layouts.inputError', ['errorName' => 'industry_category_id'])
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="branch_type_id">{{ __('Branch License') }}</label>
                        <input type="text" class="form-control font-weight-bold" value="{{ $branch->BranchType->code }} - {{ $branch->BranchType->name }}" readonly>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="max_counter">{{ __('Max Counter') }}</label>
                         <input type="text" class="form-control font-weight-bold" value="{{ $branch->max_counter }}" readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">{{ __('Description') }}</label>
                        <textarea name="description" id="" cols="" rows="3" class="form-control @error('description') is-invalid @enderror" readonly>{{old('description') ?: $branch->description}}</textarea>
                        @include('layouts.inputError', ['errorName' => 'description'])
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">{{ __('Email') }}</label>
                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{old('email') ?: $branch->email}}" readonly>
                        @include('layouts.inputError', ['errorName' => 'email'])
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="timezone">{{ __('Select Timezone') }}</label>
                        <select name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror" disabled>
                            <option value="" selected disabled>{{ __('Select Timezone') }}</option>
                            @switch($branch->country)
                                @case('Indonesia')
                                <option value="WIB" {{ $branch->timezone == 'WIB' ? 'selected' : '' }}>Indonesia {{ __('WIB') }} (UTC +7)</option>
                                <option value="WITA" {{ $branch->timezone == 'WITA' ? 'selected' : '' }}>Indonesia {{ __('WITA') }} (UTC +8)</option>
                                <option value="WIT" {{ $branch->timezone == 'WIT' ? 'selected' : '' }}>Indonesia {{ __('WIT') }} (UTC +9)</option>
                                    @break
                                @case('Singapore')
                                <option value="SGT" selected>Singapore Time - {{ __('SGT') }} (UTC+7)</option>
                                    @break
                                @case('Vietnam')
                                <option value="ICT" selected>Indochina Time - {{ __('ICT') }} (UTC+7)</option>
                                    @break
                                @case('Brunei')
                                <option value="BNT" selected>Brunei-Muara - {{ __('BNT') }} (UTC+8)</option>
                                    @break
                                @case('Thailand')
                                <option value="ICT" selected>Indochina Time - {{ __('ICT') }} (UTC+7)</option>
                                    @break
                                @case('Malaysia')
                                <option value="ICT" selected>Malaysia Time - {{ __('MYT') }} (UTC+8)</option>
                                    @break
                                @case('Timor-Leste')
                                <option value="TLT" selected>Timor Leste Time - {{ __('TLT') }} (UTC+9)</option>
                                    @break
                                @case('Qatar')
                                <option value="AST" selected>Arabian Standard Time - {{ __('AST') }} (UTC+3)</option>
                                    @break
                                @case('Saudi Arabia')
                                <option value="AST" selected>Arabian Standard Time - {{ __('AST') }} (UTC+3)</option>
                                    @break
                                @case('Kuwait')
                                <option value="AST" selected>Arabian Standard Time - {{ __('AST') }} (UTC+3)</option>
                                    @break
                                @case('Oman')
                                <option value="GST" selected>Gulf Standard Time - {{ __('GST') }} (UTC+4)</option>
                                    @break
                                @case('United Arab Emirates')
                                <option value="GST" selected>Gulf Standard Time - {{ __('GST') }} (UTC+4)</option>
                                    @break
                                @case('New Zealand')
                                <option value="NZST" selected>New Zealand Standard Time - {{ __('NZST') }} (UTC+12)</option>
                                    @break
                                @case('Australia')
                                <option value="AEST" {{ $branch->timezone == 'AEST' ? 'selected' : '' }}>Australian Eastern Standard Time - {{ __('AEST') }} (UTC+10)</option>
                                <option value="ACST"  {{ $branch->timezone == 'ACST' ? 'selected' : '' }}>Australian Central Standard Time - {{ __('ACST') }} (UTC+9:30)</option>
                                <option value="AWST"  {{ $branch->timezone == 'AWST' ? 'selected' : '' }}>Australian Western Standard Time - {{ __('AWST') }} (UTC+8)</option>
                                    @break
                                @case('United States')
                                    <option value="ET" {{ $branch->timezone == 'ET' ? 'selected' : '' }}> Eastern Time - {{ __('ET') }} (UTC-5:00)</option>
                                    <option value="CT" {{ $branch->timezone == 'CT' ? 'selected' : '' }}> Central Time - {{ __('CT') }} (UTC-6:00)</option>
                                    <option value="MT" {{ $branch->timezone == 'MT' ? 'selected' : '' }}> Mountain Time - {{ __('MT') }} (UTC-7:00)</option>
                                    <option value="MST" {{ $branch->timezone == 'MST' ? 'selected' : '' }}> Mountain Standard Time - {{ __('MST') }} (UTC-7:00)</option>
                                    <option value="PT" {{ $branch->timezone == 'PT' ? 'selected' : '' }}> Pacific Time - {{ __('PT') }} (UTC-8:00)</option>
                                    <option value="AKT" {{ $branch->timezone == 'AKT' ? 'selected' : '' }}> Alaska Time - {{ __('AKT') }} (UTC-9:00)</option>
                                    <option value="HAT" {{ $branch->timezone == 'HAT' ? 'selected' : '' }}> Hawaii-Aleutian Time - {{ __('HAT') }} (UTC-10:00)</option>
                                    <option value="ST" {{ $branch->timezone == 'ST' ? 'selected' : '' }}> Samoa Time - {{ __('ST') }} (UTC-11:00)</option>
                                    <option value="ChST" {{ $branch->timezone == 'ChST' ? 'selected' : '' }}> Chamorro Standard Time - {{ __('ChST') }} (UTC+10:00)</option>
                                @break
                                @default
                                    
                            @endswitch
                        </select>
                        @include('layouts.inputError', ['errorName' => 'country'])
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                            <label for="fixed_phone">{{ __('Fixed Phone') }}</label>
                            <input name="fixed_phone" type="text" class="form-control @error('fixed_phone') is-invalid @enderror" value="{{old('fixed_phone') ?: $branch->fixed_phone}}" readonly>
                            @include('layouts.inputError', ['errorName' => 'fixed_phone'])
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile_phone">{{ __('Mobile Phone') }}</label>
                            <input name="mobile_phone" type="text" class="form-control @error('mobile_phone') is-invalid @enderror" value="{{old('mobile_phone') ?: $branch->mobile_phone}}" readonly>
                            @include('layouts.inputError', ['errorName' => 'mobile_phone'])
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="is_active">{{ __('Show in Mobile') }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="inlineRadio1" value="1" {{ $branch->is_active ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="inlineRadio1">{{ __('Yes') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="inlineRadio2" value="0" {{ !$branch->is_active ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="inlineRadio2">{{ __('No') }}</label>
                            </div>
                        </div>
                        @include('layouts.inputError', ['errorName' => 'is_active'])
                    </div>
                    <div class="col-md-12 text-right">
                        <button class="btn px-5" type="button" style="background-color:#103C7C; color:#fff" onclick="onEdit(this)">
                            Edit
                        </button>
                        <button class="btn btn-outline-dark px-5 d-none mr-2" type="button" id="cancelBtn" onclick="onCancel(this)">
                            Cancel
                        </button>
                        <button class="btn btn-danger px-5 d-none" type="submit" id="submitBtn">
                            Save
                        </button>
                    </div>
            </div>


            {{-- <div class="form-group">
                    <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror" disabled>
                        <option value="1">{{ __('Yes') }}</option>
                        <option value="0">{{ __('No') }}</option>
                    </select>
                </div> --}}

            {{-- <button type="submit" class="btn btn-warning fullwidth d-none mb-3">{{ __('Update') }}</button> --}}
        </form>
    </div>
</div>

@push('js')
    <script>
        function onEdit(e) {
            $(e).addClass('d-none');
            $('#cancelBtn').removeClass('d-none');
            $('#submitBtn').removeClass('d-none');
            $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', false);
            $('input[type="file"], select,input[type="radio"]').prop('disabled', false);
            $('.custom-upload').removeClass('d-none').addClass('d-flex');
        }

        function onCancel(e){
            $('#editFORM')[0].reset();
            $('button[onclick="onEdit(this)"]').removeClass('d-none');
            $('#cancelBtn').addClass('d-none');
            $('#submitBtn').addClass('d-none');
            $('input[type="text"], input[type="email"], input[type="number"], textarea').prop('readonly', true);
            $('input[type="file"], select,input[type="radio"]').prop('disabled', true);
            $('.custom-upload').removeClass('d-flex').addClass('d-none');
            resetImagePreview(1);
            resetImagePreview(2);
        }

        function previewImage(input, mediaNo) {
           const file = input.files[0];
            const preview = document.getElementById('preview' + mediaNo);
            const original = document.getElementById('imgOriginal' + mediaNo);
            const placeholder = document.getElementById('imgPlaceholder' + mediaNo);

            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar.');
                input.value = '';
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran maksimal 10MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');

                if (original) original.classList.add('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            };

            reader.readAsDataURL(file);
        }

        
    function resetImagePreview(mediaNo) {
        const input = document.getElementById('fileInput' + mediaNo);
        const preview = document.getElementById('preview' + mediaNo);
        const original = document.getElementById('imgOriginal' + mediaNo);
        const placeholder = document.getElementById('imgPlaceholder' + mediaNo);

        input.value = '';
        preview.src = '';
        preview.classList.add('d-none');

        if (original) original.classList.remove('d-none');
        if (placeholder && !original) placeholder.classList.remove('d-none');
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