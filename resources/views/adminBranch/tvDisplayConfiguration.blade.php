@extends('layouts.app')

@push('css')
    <style>
        .monitor-images-wrapper {
            display: flex;
            flex-direction: column;
            gap: .875rem;
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

        .monitor-image-upload img,
        .monitor-image-upload div img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hidden {
            display: none;
        }

        .monitor-image-container {
            display: flex;
            gap: 1rem;
        }

        .delete-image-button {
            color: #dc3545;
            font-size: .875rem;
            border: none;
            background-color: rgba(220, 53, 69, .1);
            padding: .2rem .625rem;
            border-radius: 6px;
        }

        .layout-img-container {
            max-width: 500px;
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

        .layout-labels {
            display: flex;
            gap: 1rem;
        }

        .layout-label-item {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .layout-img {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background-color: #DDDDDD;
        }

        .wrapper-submit {
            display: flex;
            justify-content: flex-end;
        }

        .wrapper-group-action {
            border: 1px solid #DDDDDD;
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
    </style>
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="font-weight-bold text-primary mb-0">
                Manajemen Display TV
            </h6>
        </div>

        <div class="card-body">
            @include('layouts.alert')

        <div class="row">
            <div class="col-md-5 col-sm-12 mb-5">
                <div class="mb-4">
                    <h5 class="font-weight-bold">Display Iklan</h5>
                    <p class="text-caption">Tambahkan iklan untuk ditampilkan di monitor antrian</p>
                </div>

                <form action="{{ route('admin-branch.branch-configuration.queue-monitor.update', Auth::user()->branch_id) }}" id="image_form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="monitor-images-wrapper mb-3">
                        <div class="monitor-image-container">
                            <label for="image_1">
                                <div class="monitor-image-upload">
                                    {{-- <img src="{{ asset($image_1) }}" id="preview_image_1"> --}}
                                    @if (pathinfo($image_1, PATHINFO_EXTENSION) === 'mp4')
                                        <video width="100%" height="100%" id="preview_media" autoplay muted>
                                            <source src="{{ asset($image_1) }}" type="video/mp4">
                                        </video>
                                    @else
                                        <img src="{{ asset($image_1) }}" id="preview_media">
                                    @endif

                                    <input type="file" accept="image/*, video/mp4" name="image_1" id="image_1"
                                        onchange="previewMedia(this, 1)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>

                            <div>
                                <div class="mb-1">Gambar / Video Iklan 1</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_1"
                                        onclick="deleteMedia(1)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="monitor-image-container">
                            <label for="image_2">
                                <div class="monitor-image-upload">
                                    <img src="{{ asset($image_2) }}" id="preview_image_2">

                                <input type="file" accept="image/*" name="image_2" id="image_2" onchange="previewImage(this, 2)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>

                            <div>
                                <div class="mb-1">Gambar Iklan 2</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_2" onclick="deleteImage(2)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="monitor-image-container">
                            <label for="image_3">
                                <div class="monitor-image-upload">
                                    <img src="{{ asset($image_3) }}" id="preview_image_3">

                                <input type="file" accept="image/*" name="image_3" id="image_3" onchange="previewImage(this, 3)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>
                            <div>
                                <div class="mb-1">Gambar Iklan 3</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_3" onclick="deleteImage(3)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="monitor-image-container" id="monitor-container-image-4">
                            <label for="image_4">
                                <div class="monitor-image-upload">
                                    <img src="{{ asset($image_4) }}" id="preview_image_4">

                                <input type="file" accept="image/*" name="image_4" id="image_4" onchange="previewImage(this, 4)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>
                            <div>
                                <div class="mb-1">Gambar Iklan 4</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_4" onclick="deleteImage(4)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="monitor-image-container" id="monitor-container-image-5">
                            <label for="image_5">
                                <div class="monitor-image-upload">
                                    <img src="{{ asset($image_5) }}" id="preview_image_5">

                                <input type="file" accept="image/*" name="image_5" id="image_5" onchange="previewImage(this, 5)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>
                            <div>
                                <div class="mb-1">Gambar Iklan 5</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_5" onclick="deleteImage(5)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="monitor-image-container" id="monitor-container-image-6">
                            <label for="image_6">
                                <div class="monitor-image-upload">
                                    <img src="{{ asset($image_6) }}" id="preview_image_6">

                                <input type="file" accept="image/*" name="image_6" id="image_6" onchange="previewImage(this, 6)" hidden>

                                    <span class="monitor-image-label">
                                        <span class="fas fa-upload"></span>
                                    </span>
                                </div>
                            </label>
                            <div>
                                <div class="mb-1">Gambar Iklan 6</div>
                                <div>
                                    <button type="button" class="delete-image-button hidden" id="delete_button_6" onclick="deleteImage(6)">
                                        <span class="fas fa-times mr-1"></span>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submit_image" class="btn btn-warning hidden">Unggah Gambar</submit>
                </form>
            </div>

            <div class="col-md-7 col-sm-12">
                <form action="{{ route('admin-branch.branch-configuration.queue-monitor.update-layout', Auth::user()->branch_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Layout Display</h5>
                        <p class="text-caption">Pilih jenis layout yang sesuai dengan bisnis Anda</p>
                    </div>

                    <div>
                        <div class="form-group" style="width: 200px;">
                            <select name="template_signage" class="form-control" onchange="changeLayout(this)">
                                <option value="standard-ui" {{ $template_signage != 'standard-ui' ?: 'selected' }} >Standard UI</option>
                                @if (!$is_appointment)
                                    <option value="custom-layout-1" {{ $template_signage == 'custom-layout-1' ? 'selected' : '' }}>Custom Layout 1</option>
                                    <option value="custom-layout-2" {{ $template_signage == 'custom-layout-2' ? 'selected' : '' }}>Custom Layout 2</option>
                                    <option value="custom-layout-3" {{ $template_signage == 'custom-layout-3' ? 'selected' : '' }}>Custom Layout 3</option>
                                @endif
                            </select>
                        </div>

                        <div class="layout-img-container mb-3">
                            <img src="{{ asset($default_image_layout) }}" alt="Display image layout" id="display-image-layout">
                        </div>

                        <div class="layout-labels">
                            <div class="layout-label-item">
                                <div class="layout-img" style="background-color: #EADAA4;"></div>
                                <span>Space Iklan</span>
                            </div>

                            <div class="layout-label-item">
                                <div class="layout-img" style="background-color: #F6A2FD;"></div>
                                <span>Sedang Dilayani</span>
                            </div>

                            <div class="layout-label-item">
                                <div class="layout-img" style="background-color: #93F097;"></div>
                                <span>Antrian Menunggu</span>
                            </div>
                        </div>

                        <div class="wrapper-submit mt-3">
                            <button type="submit" class="btn btn-warning">Simpan</submit>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-5 col-sm-12 mb-5">
                <form action="{{ route('admin-branch.branch-configuration.queue-monitor.update-token', Auth::user()->branch_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Perbarui Token</h5>
                        <p class="text-caption">Perbarui Token Web Monitor TV</p>
                    </div>

                    <button type="submit" id="submit_image" class="btn btn-primary">Perbarui</submit>
                </form>
            </div>
        </div>
    </div>
</div>

@if (!$is_appointment)
    <div id='layoutConfig2' class="card shadow mb-4 {{ $template_signage == 'custom-layout-2' ?: 'd-none'}}">
        <div class="card-header">
            <h6 class="font-weight-bold text-primary mb-0">
                Konfigurasi Display TV
            </h6>
        </div>

        <div class="card-body">
            @include('layouts.alert')

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="mt-4 col-md-12">
                        <div class="mb-4 d-flex flex-column align-items-center">
                            <h5 class="font-weight-bold">Konfigurasi Layout Display</h5>
                            <p class="text-caption">Atur tampilan yang akan di tampilkan pada monitor antrian</p>
                        </div>

                        <form action="{{ route('admin-branch.branch-configuration.queue-monitor.update-custom-layout', Auth::user()->branch_id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row justify-content-around">
                                @csrf
                                @method('PUT')
                                <div class="wrapper-group-action col-md-5">
                                    <b>{{ __('Background') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="background_type">{{ __('Background Type') }}</label>
                                                <select
                                                    name="background_type"
                                                    class="form-control @error('background_type') is-invalid @enderror"
                                                    onchange="changeBackgroundType(this, 1)"
                                                    required
                                                >
                                                    <option value="color" {{ $layout_configuration->background_type != 'color' ?: 'selected' }} >Color</option>
                                                    <option value="image" {{ $layout_configuration->background_type != 'image' ?: 'selected' }} >Image</option>
                                                </select>
                                            </div>
                                            <div class="form-group {{ $layout_configuration->background_type != 'color' ?: 'd-none' }}" id="background_image_wrapper_1">
                                                <label>{{ __('Background Image') }}</label>
                                                <div class="monitor-image-container">
                                                    <label for="background_image_1">
                                                        <div class="monitor-image-upload">
                                                            <img src="{{ asset($layout_configuration->background_image) }}" id="preview_background_image_1">

                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                name="background_image"
                                                                id="background_image_1"
                                                                onchange="previewBackgroundImage(this, 1)"
                                                                hidden
                                                            >

                                                            <span class="monitor-image-label">
                                                                <span class="fas fa-upload"></span>
                                                            </span>
                                                        </div>
                                                    </label>

                                                    <div>
                                                        <button type="button" class="delete-image-button d-none" id="delete_button_background_image_1" onclick="deleteImg()">
                                                            <span class="fas fa-times mr-1"></span>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $layout_configuration->background_type != 'image' ?: 'd-none' }}" id="background_color_1">
                                                <label for="background_color">{{ __('Background Color') }}</label>
                                                <input
                                                    name="background_color"
                                                    type="color"
                                                    class="form-control form-input-color @error('background_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->background_color ?? old('background_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-6">
                                    <b>{{ __('Font Color') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="datetime_color">{{ __('Date Time') }}</label>
                                                <input
                                                    name="datetime_color"
                                                    type="color"
                                                    class="form-control @error('datetime_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->datetime_color ?? old('datetime_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="sidebar_subtitle_color">{{ __('Waiting Label') }}</label>
                                                <input
                                                    name="sidebar_subtitle_color"
                                                    type="color"
                                                    class="form-control @error('sidebar_subtitle_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->sidebar_subtitle_color ?? old('sidebar_subtitle_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="waiting_list_font_color">{{ __('Queue Number') }}</label>
                                                <input
                                                    name="waiting_list_font_color"
                                                    type="color"
                                                    class="form-control @error('waiting_list_font_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->waiting_list_font_color ?? old('waiting_list_font_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_font_header_color">{{ __('Calling Card Title') }}</label>
                                                <input
                                                    name="calling_card_font_header_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_font_header_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_font_header_color ?? old('calling_card_font_header_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="font_queue_first_letter_color">{{ __('First Letter Call Number') }}</label>
                                                <input
                                                    name="font_queue_first_letter_color"
                                                    type="color"
                                                    class="form-control @error('font_queue_first_letter_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->font_queue_first_letter_color ?? old('font_queue_first_letter_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="font_queue_color">{{ __('Next Letter Call Number') }}</label>
                                                <input
                                                    name="font_queue_color"
                                                    type="color"
                                                    class="form-control @error('font_queue_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->font_queue_color ?? old('font_queue_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="running_text_color">{{ __('Running Text') }}</label>
                                                <input
                                                    name="running_text_color"
                                                    type="color"
                                                    class="form-control @error('running_text_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text_color ?? old('running_text_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-5">
                                    <b>{{ __('Card') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="waiting_list_card_color">{{ __('Waiting Card List') }}</label>
                                                <input
                                                    name="waiting_list_card_color"
                                                    type="color"
                                                    class="form-control @error('waiting_list_card_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->waiting_list_card_color ?? old('waiting_list_card_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_header_color">{{ __('Header Calling Card') }}</label>
                                                <input
                                                    name="calling_card_header_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_header_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_header_color ?? old('calling_card_header_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_body_color">{{ __('Body Calling Card') }}</label>
                                                <input
                                                    name="calling_card_body_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_body_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_body_color ?? old('calling_card_body_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-6">
                                    <b>{{ __('Running Text') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="running_text">{{ __('Running Text') }}</label>
                                                <input
                                                    name="running_text"
                                                    type="text"
                                                    class="form-control @error('running_text') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text ?? old('running_text')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="running_text_speed">{{ __('Speed') }}</label>
                                                <input
                                                    name="running_text_speed"
                                                    type="number"
                                                    class="form-control @error('running_text_speed') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text_speed ?? old('running_text_speed')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wrapper-submit mt-3">
                                <button type="submit" class="btn btn-warning">Simpan</submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id='layoutConfig3' class="card shadow mb-4 {{ $template_signage == 'custom-layout-3' ?: 'd-none'}}">
        <div class="card-header">
            <h6 class="font-weight-bold text-primary mb-0">
                Konfigurasi Display TV
            </h6>
        </div>

        <div class="card-body">
            @include('layouts.alert')

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="mt-4 col-md-12">
                        <div class="mb-4 d-flex flex-column align-items-center">
                            <h5 class="font-weight-bold">Konfigurasi Layout Display</h5>
                            <p class="text-caption">Atur tampilan yang akan di tampilkan pada monitor antrian</p>
                        </div>

                        <form action="{{ route('admin-branch.branch-configuration.queue-monitor.update-custom-layout', Auth::user()->branch_id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row justify-content-around">
                                @csrf
                                @method('PUT')
                                <div class="wrapper-group-action col-md-5">
                                    <b>{{ __('Background') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="background_type">{{ __('Background Type') }}</label>
                                                <select
                                                    name="background_type"
                                                    class="form-control @error('background_type') is-invalid @enderror"
                                                    onchange="changeBackgroundType(this, 2)"
                                                    required
                                                >
                                                    <option value="color" {{ $layout_configuration->background_type != 'color' ?: 'selected' }} >Color</option>
                                                    <option value="image" {{ $layout_configuration->background_type != 'image' ?: 'selected' }} >Image</option>
                                                </select>
                                            </div>
                                            <div class="form-group {{ $layout_configuration->background_type != 'color' ?: 'd-none' }}" id="background_image_wrapper_2">
                                                <label>{{ __('Background Image') }}</label>
                                                <div class="monitor-image-container">
                                                    <label for="background_image_2">
                                                        <div class="monitor-image-upload">
                                                            <img src="{{ asset($layout_configuration->background_image) }}" id="preview_background_image_2">

                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                name="background_image"
                                                                id="background_image_2"
                                                                onchange="previewBackgroundImage(this, 2)"
                                                                hidden
                                                            >

                                                            <span class="monitor-image-label">
                                                                <span class="fas fa-upload"></span>
                                                            </span>
                                                        </div>
                                                    </label>

                                                    <div>
                                                        <button type="button" class="delete-image-button d-none" id="delete_button_background_image_2" onclick="deleteImg()">
                                                            <span class="fas fa-times mr-1"></span>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group {{ $layout_configuration->background_type != 'image' ?: 'd-none' }}" id="background_color_2">
                                                <label for="background_color">{{ __('Background Color') }}</label>
                                                <input
                                                    name="background_color"
                                                    type="color"
                                                    class="form-control form-input-color @error('background_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->background_color ?? old('background_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-6">
                                    <b>{{ __('Font Color') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="datetime_color">{{ __('Date Time') }}</label>
                                                <input
                                                    name="datetime_color"
                                                    type="color"
                                                    class="form-control @error('datetime_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->datetime_color ?? old('datetime_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="sidebar_subtitle_color">{{ __('name.module', ['module' => __('Branch')]) }}</label>
                                                <input
                                                    name="sidebar_subtitle_color"
                                                    type="color"
                                                    class="form-control @error('sidebar_subtitle_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->sidebar_subtitle_color ?? old('sidebar_subtitle_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="waiting_list_font_color">{{ __('Counter') }}</label>
                                                <input
                                                    name="waiting_list_font_color"
                                                    type="color"
                                                    class="form-control @error('waiting_list_font_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->waiting_list_font_color ?? old('waiting_list_font_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="font_queue_first_letter_color">{{ __('First Letter Call Number') }}</label>
                                                <input
                                                    name="font_queue_first_letter_color"
                                                    type="color"
                                                    class="form-control @error('font_queue_first_letter_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->font_queue_first_letter_color ?? old('font_queue_first_letter_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="font_queue_color">{{ __('Next Letter Call Number') }}</label>
                                                <input
                                                    name="font_queue_color"
                                                    type="color"
                                                    class="form-control @error('font_queue_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->font_queue_color ?? old('font_queue_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_font_header_color">Label Counter</label>
                                                <input
                                                    name="calling_card_font_header_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_font_header_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_font_header_color ?? old('calling_card_font_header_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="running_text_color">{{ __('Running Text') }}</label>
                                                <input
                                                    name="running_text_color"
                                                    type="color"
                                                    class="form-control @error('running_text_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text_color ?? old('running_text_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-5">
                                    <b>{{ __('Card') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_body_color">Card List Counter</label>
                                                <input
                                                    name="calling_card_body_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_body_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_body_color ?? old('calling_card_body_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="waiting_list_card_color">Card Label Counter</label>
                                                <input
                                                    name="waiting_list_card_color"
                                                    type="color"
                                                    class="form-control @error('waiting_list_card_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->waiting_list_card_color ?? old('waiting_list_card_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="calling_card_header_color">Background Text Berjalan</label>
                                                <input
                                                    name="calling_card_header_color"
                                                    type="color"
                                                    class="form-control @error('calling_card_header_color') is-invalid @enderror"
                                                    value="{{$layout_configuration->calling_card_header_color ?? old('calling_card_header_color')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wrapper-group-action col-md-6">
                                    <b>{{ __('Running Text') }}</b>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="running_text">{{ __('Running Text') }}</label>
                                                <input
                                                    name="running_text"
                                                    type="text"
                                                    class="form-control @error('running_text') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text ?? old('running_text')}}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="running_text_speed">{{ __('Speed') }}</label>
                                                <input
                                                    name="running_text_speed"
                                                    type="number"
                                                    class="form-control @error('running_text_speed') is-invalid @enderror"
                                                    value="{{$layout_configuration->running_text_speed ?? old('running_text_speed')}}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wrapper-submit mt-3">
                                <button type="submit" class="btn btn-warning">Simpan</submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>

    const imageLayouts = @json($image_layouts);

    function changeLayout(input) {
        const { value } = input;

        const { image } = imageLayouts.find((obj) => obj.key == value);

        if (value == 'custom-layout-2') {
            document.getElementById("layoutConfig2").classList.remove("d-none")
        } else {
            document.getElementById("layoutConfig2").classList.add("d-none")
        }

        if (value == 'custom-layout-3') {
            document.getElementById("layoutConfig3").classList.remove("d-none")
        } else {
            document.getElementById("layoutConfig3").classList.add("d-none")
        }

        if (value != 'custom-layout-2' && value != 'custom-layout-3') {
            document.getElementById("monitor-container-image-4").classList.add("d-none")
            document.getElementById("monitor-container-image-5").classList.add("d-none")
            document.getElementById("monitor-container-image-6").classList.add("d-none")
        } else {
            document.getElementById("monitor-container-image-4").classList.remove("d-none")
            document.getElementById("monitor-container-image-5").classList.remove("d-none")
            document.getElementById("monitor-container-image-6").classList.remove("d-none")
        }

        const documentImage = document.getElementById("display-image-layout");
        documentImage.src = `${window.location.origin}/${image}`
    }

    function changeBackgroundType(input, priority) {
        const { value } = input;

        if (value == 'image') {
            document.getElementById(`background_color_${priority}`).classList.add("d-none")
            document.getElementById(`background_image_wrapper_${priority}`).classList.toggle("d-none")
        } else {
            document.getElementById(`background_color_${priority}`).classList.toggle("d-none")
            document.getElementById(`background_image_wrapper_${priority}`).classList.add("d-none")
        }
    }

    function previewBackgroundImage(input, imageNo) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.readAsDataURL(input.files[0]);

            reader.onload = function(e) {
                $('#preview_background_image_' + imageNo).attr('src', e.target.result);
                $('#delete_button_background_image_' + imageNo).removeClass('hidden');
            }
        }
    }

    function previewMedia(input, mediaNo) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (input.files[0].type.startsWith('image/')) {
                    $('#image_' + mediaNo).prevAll().remove();
                    $('#image_' + mediaNo).before(
                        `<img src="${e.target.result}" id="preview_media">`
                    );
                } else if (input.files[0].type === 'video/mp4') {
                    $('#image_' + mediaNo).prevAll().remove();
                    $('#image_' + mediaNo).before(`
                    <video width="100%" height="100%" id="preview_media" autoplay muted>
                        <source src="${e.target.result}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                `);
                }

                $('#delete_button_' + mediaNo).removeClass('hidden');
                $('#submit_image').removeClass('hidden');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewImage(input, imageNo) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.readAsDataURL(input.files[0]);

            reader.onload = function(e) {
                $('#preview_image_' + imageNo).attr('src', e.target.result);
                $('#delete_button_' + imageNo).removeClass('hidden');
                $('#submit_image').removeClass('hidden');
            }
        }
    }

    function deleteMedia(mediaNo) {
        let mediaSrc = '{{ asset($image_1) }}';
        let mediaImg = mediaSrc.includes("tv_images");

        if (mediaSrc) {
            $('#image_' + mediaNo).prevAll().remove();
            $('#image_' + mediaNo).before(mediaImg ?
                `<img src="${mediaSrc}" id="preview_media" style="object-fit: cover;">` :
                `<video width="100%" height="100%" id="preview_media" autoplay muted>
                <source src="${mediaSrc}" type="video/mp4">
                Your browser does not support the video tag.
            </video>`
            );
        } else {
            $('#image_' + mediaNo).prevAll().remove();
            $('#image_' + mediaNo).before('');
        }

        $('#image_' + mediaNo).val(null);
        $('#delete_button_' + mediaNo).addClass('hidden');
    }

    function deleteImage(imageNo) {
        let imageSrc = ''

        if (imageNo === 1) {
            imageSrc = '{{ asset($image_1) }}'
        } else if (imageNo === 2) {
            imageSrc = '{{ asset($image_2) }}'
        } else if (imageNo === 3) {
            imageSrc = '{{ asset($image_3) }}'
        } else if (imageNo === 4) {
            imageSrc = '{{ asset($image_4) }}'
        } else if (imageNo === 5) {
            imageSrc = '{{ asset($image_5) }}'
        } else if (imageNo === 6) {
            imageSrc = '{{ asset($image_6) }}'
        }

        if (imageSrc) {
            $('#preview_image_' + imageNo).attr('src', imageSrc);
        } else {
            $('#preview_image_' + imageNo).removeAttr('src');
        }

        $('#image_' + imageNo).val(null);
        $('#delete_button_' + imageNo).addClass('hidden');
    }

    function deleteImg(priority) {
        const imageSrc = '{{ asset($defaultImage) }}'

        $(`#preview_background_image`).attr('src', imageSrc);

        $(`#background_image`).val(null);
        $(`#delete_button_background_image`).addClass('display-none');
    }
</script>
@endsection
