@extends('layouts.app')

@push('css')
    <style>
        .layout-img-container {
            max-width: 400px;
            width: 100%;
            border: 2px solid #DDDDDD;
            border-radius: 6px;
            overflow: hidden;
        }

        .layout-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .display-none {
            display: none;
        }

        .display-blok {
            display: block;
        }

        .delete-image-button {
            color: #dc3545;
            font-size: .875rem;
            border: none;
            background-color: rgba(220, 53, 69, .1);
            padding: .2rem .625rem;
            border-radius: 6px;
        }
        .monitor-image-container {
            display: flex;
            gap: 1rem;
        }
        
        .monitor-image-upload {
            width: 100px;
            height: 60px;
            background-color: #ddd;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }

        .monitor-image-upload:hover:after {
            content: '';
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #189DCD;
            box-sizing: border-box;
            position: absolute;
            display: block;
            background-color: rgba(24, 157, 205, 0.15);
            z-index: 1;
            border-radius: 6px;
        }

        .monitor-image-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #189DCD;
            z-index: 2;
            font-size: 1.5rem;
            display: none;
        }

        .monitor-image-upload:hover .monitor-image-label {
            display: inline-block;
        }

        .monitor-image-upload img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-input-color {
            width: 100px;
        }

        .wrapper-form-footer {
            display: flex;
            justify-content: end;
        }

        .wrapper-group-action {
            border: 1px solid #DDDDDD;
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
        }

    </style>
@endpush

@section('content')

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="font-weight-bold text-primary mb-0">
            Manajemen Webkiosk UI
        </h6>
    </div>

    <div class="card-body">
        @include('layouts.alert')

        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('admin-branch.branch-configuration.webkiosk.update', Auth::user()->branch_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-4">
                                <h5 class="font-weight-bold">Layout Display</h5>
                                <p class="text-caption">Pilih jenis layout yang sesuai dengan bisnis Anda</p>
                            </div>

                            <div>
                                <div class="form-group" style="width: 200px;">
                                    <select id="select-layout" name="layout" class="form-control" onchange="changeLayout(this)">
                                        @foreach ($layouts as $layout)
                                            <option value="{{$layout->id}}" {{ $webkiosConfiguration->layout != $layout->id ?: 'selected' }}>{{ $layout->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="layout-img-container mb-3">
                                    <img id="image_layout" src="{{asset($layouts[(int)$webkiosConfiguration->layout -1]->image)}}" alt="{{$layouts[(int)$webkiosConfiguration->layout -1]->name}}">
                                </div>
                            </div>
                        </div>

                        <div id='layoutConfig' class="col-md-8 {{ $webkiosConfiguration->layout != '1' ?: 'display-none'}}">
                            <div class="mb-4">
                                <h5 class="font-weight-bold">Konfigurasi Layout Display</h5>
                                <p class="text-caption">Atur tampilan yang akan di tampilkan pada monitor antrian</p>
                            </div>

                            <div>
                                <div class="wrapper-group-action">
                                    <b>{{ __('Background') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="primary_background_type">{{ __('Primary Type') }}</label>
                                                <select
                                                    name="primary_background_type"
                                                    class="form-control @error('primary_background_type') is-invalid @enderror"
                                                    onchange="changeBackgroundType(this, 'primary')"
                                                    required
                                                >
                                                    <option value="color" {{ $webkiosConfiguration->primary_background_type != 'color' ?: 'selected' }} >Color</option>
                                                    <option value="image" {{ $webkiosConfiguration->primary_background_type != 'image' ?: 'selected' }} >Image</option>
                                                </select>
                                            </div>
                                            <div class="form-group {{ $webkiosConfiguration->primary_background_type != 'color' ?: 'display-none' }}" id="primary_background_image_wrapper">
                                                <label>{{ __('Background Image') }}</label>
                                                <div class="monitor-image-container">
                                                    <label for="primary_background_image">
                                                        <div class="monitor-image-upload">
                                                            <img src="{{ asset($webkiosConfiguration->primary_background_image) }}" id="preview_primary_background_image">

                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                name="primary_background_image"
                                                                id="primary_background_image"
                                                                onchange="previewImage(this, 'primary')"
                                                                hidden
                                                            >

                                                            <span class="monitor-image-label">
                                                                <span class="fas fa-upload"></span>
                                                            </span>
                                                        </div>
                                                    </label>

                                                    <div>
                                                        <button type="button" class="delete-image-button display-none" id="delete_button_primary_background_image" onclick="deleteImage('primary')">
                                                            <span class="fas fa-times mr-1"></span>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $webkiosConfiguration->primary_background_type != 'image' ?: 'display-none' }}" id="primary_background_color">
                                                <label for="primary_background_color">{{ __('Background Color') }}</label>
                                                <input
                                                    name="primary_background_color" 
                                                    type="color" 
                                                    class="form-control form-input-color @error('primary_background_color') is-invalid @enderror"
                                                    value="{{$webkiosConfiguration->primary_background_color ?? old('primary_background_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="secondary_background_type">{{ __('Secondary Type') }}</label>
                                                <select 
                                                    name="secondary_background_type" 
                                                    class="form-control @error('secondary_background_type') is-invalid @enderror" 
                                                    onchange="changeBackgroundType(this, 'secondary')" 
                                                    required
                                                >
                                                    <option value="color" {{ $webkiosConfiguration->secondary_background_type != 'color' ?: 'selected' }} >Color</option>
                                                    <option value="image" {{ $webkiosConfiguration->secondary_background_type != 'image' ?: 'selected' }} >Image</option>
                                                </select>
                                            </div>
                                            <div class="form-group {{ $webkiosConfiguration->secondary_background_type != 'color' ?: 'display-none' }}" id="secondary_background_image_wrapper">
                                                <label>{{ __('Background Image') }}</label>
                                                <div class="monitor-image-container">
                                                    <label for="secondary_background_image">
                                                        <div class="monitor-image-upload">
                                                            <img src="{{ asset($webkiosConfiguration->secondary_background_image) }}" id="preview_secondary_background_image">

                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                name="secondary_background_image"
                                                                id="secondary_background_image"
                                                                onchange="previewImage(this, 'secondary')"
                                                                hidden
                                                            >

                                                            <span class="monitor-image-label">
                                                                <span class="fas fa-upload"></span>
                                                            </span>
                                                        </div>
                                                    </label>

                                                    <div>
                                                        <button type="button" class="delete-image-button display-none" id="delete_button_secondary_background_image" onclick="deleteImage('secondary')">
                                                            <span class="fas fa-times mr-1"></span>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $webkiosConfiguration->secondary_background_type != 'image' ?: 'display-none' }}" id="secondary_background_color">
                                                <label for="secondary_background_color">{{ __('Background Color') }}</label>
                                                <input
                                                    name="secondary_background_color" 
                                                    type="color" 
                                                    class="form-control form-input-color @error('secondary_background_color') is-invalid @enderror"
                                                    value="{{$webkiosConfiguration->secondary_background_color ?? old('secondary_background_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="wrapper-group-action">
                                    <b>{{ __('Button') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label for="button_background_color">{{ __('Background') }}</label>
                                                <input
                                                    name="button_background_color" 
                                                    type="color" 
                                                    class="form-control @error('button_background_color') is-invalid @enderror"
                                                    value="{{$webkiosConfiguration->button_background_color ?? old('button_background_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label for="botton_border_color">{{ __('Border') }}</label>
                                                <input
                                                    name="botton_border_color" 
                                                    type="color" 
                                                    class="form-control @error('botton_border_color') is-invalid @enderror"
                                                    value="{{$webkiosConfiguration->botton_border_color ?? old('botton_border_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label for="font_color">{{ __('Font') }}</label>
                                                <input
                                                    name="font_color" 
                                                    type="color" 
                                                    class="form-control @error('font_color') is-invalid @enderror"
                                                    value="{{$webkiosConfiguration->font_color ?? old('font_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 wrapper-form-footer">
                            <button type="submit" id="submit_image" class="btn btn-warning hidden">Simpan</submit>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        const layouts = @json($layouts);

        function changeLayout(input) {
            const { value } = input;

            if (value != 1) {
                document.getElementById("layoutConfig").classList.toggle("display-none")
            } else {
                document.getElementById("layoutConfig").classList.add("display-none")
            }

            const { image, name } = layouts.find((obj) => obj.id == value);

            const documentImage = document.getElementById("image_layout");
            documentImage.src = `${window.location.origin}/${image}`
            documentImage.alt = name;
        }

        function changeBackgroundType(input, priority) {
            const { value } = input;

            if (value == 'image') {
                if (priority == 'primary') {
                    document.getElementById("primary_background_color").classList.add("display-none")
                    document.getElementById("primary_background_image_wrapper").classList.toggle("display-none")
                } else {
                    document.getElementById("secondary_background_color").classList.add("display-none")
                    document.getElementById("secondary_background_image_wrapper").classList.toggle("display-none")
                }
            } else {
                if (priority == 'primary') {
                    document.getElementById("primary_background_color").classList.toggle("display-none")
                    document.getElementById("primary_background_image_wrapper").classList.add("display-none")
                } else {
                    document.getElementById("secondary_background_color").classList.toggle("display-none")
                    document.getElementById("secondary_background_image_wrapper").classList.add("display-none")
                }
            }
        }

        function previewImage(input, priority)  {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.readAsDataURL(input.files[0]);

                reader.onload = function (e) {
                    $(`#preview_${priority}_background_image`).attr('src', e.target.result);
                    $(`#delete_button_${priority}_background_image`).removeClass('display-none');
                }
            }
        }

        function deleteImage(priority) {
            
            const imageSrc = '{{ asset($defaultImage) }}'

            $(`#preview_${priority}_background_image`).attr('src', imageSrc);

            $(`#${priority}_background_image`).val(null);
            $(`#delete_button_${priority}_background_image`).addClass('display-none');
        }
    </script>
@endpush
@endsection