@extends('layouts.app')

@push('css')
    <style>
        .monitor-images-wrapper  {
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

        .monitor-image-upload img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            display: none;
        }
    </style>
@endpush

@section('content')
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="font-weight-bold text-primary">
            Manajemen Display TV
        </h6>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <div class="mb-4">
                    <h5 class="font-weight-bold">Display Iklan</h5>
                    <p class="text-caption">Tambahkan iklan untuk ditampilkan di monitor antrian</p>
                </div>

                <div class="monitor-images-wrapper">       
                    <div class="monitor-image-container">
                        <label for="image_1">
                            <div class="monitor-image-upload">
                                <img id="preview_image_1">

                                <input type="file" name="image_1" id="image_1" onchange="previewImage(this, 1)" hidden>

                                <span class="monitor-image-label">
                                    <span class="fas fa-upload"></span>
                                </span>
                            </div>
                        </label>

                        <div>
                            <div class="mb-1">Gambar Iklan 1</div>
                            <div>
                                <button class="delete-image-button" id="delete_button_1" onclick="deleteImage(1)">
                                    <span class="fas fa-times mr-1"></span>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="monitor-image-container">
                        <label for="image_2">
                            <div class="monitor-image-upload">
                                <img id="preview_image_2">

                                <input type="file" name="image_2" id="image_2" onchange="previewImage(this, 2)" hidden>

                                <span class="monitor-image-label">
                                    <span class="fas fa-upload"></span>
                                </span>
                            </div>
                        </label>

                        <div>
                            <div class="mb-1">Gambar Iklan 2</div>
                            <div>
                                <button class="delete-image-button" id="delete_button_2" onclick="deleteImage(2)">
                                    <span class="fas fa-times mr-1"></span>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="monitor-image-container">
                        <label for="image_3">
                            <div class="monitor-image-upload">
                                <img id="preview_image_3">

                                <input type="file" name="image_3" id="image_3" onchange="previewImage(this, 3)" hidden>

                                <span class="monitor-image-label">
                                    <span class="fas fa-upload"></span>
                                </span>
                            </div>
                        </label>

                        <div>
                            <div class="mb-1">Gambar Iklan 3</div>
                            <div>
                                <button class="delete-image-button" id="delete_button_3" onclick="deleteImage(3)">
                                    <span class="fas fa-times mr-1"></span>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="mb-4">
                    <h5 class="font-weight-bold">Layout Display</h5>
                    <p class="text-caption">Pilih jenis layout yang sesuai dengan bisnis Anda</p>
                </div>

                <div>
                    <div class="form-group" style="width: 200px;">
                        <select class="form-control">
                            <option value="Layout 1" selected>Layout 1</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input, imageNo)  {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);

            reader.onload = function (e) {
                $('#preview_image_' + imageNo).attr('src', e.target.result);
                $('#delete_button_' + imageNo).show();
            }
        }
    }

    function deleteImage(imageNo) {
        $('#image_' + imageNo).val('');
        $('#preview_image_' + imageNo).removeAttr('src');
        $('#delete_button_' + imageNo).hide();
    }
</script>

@endsection