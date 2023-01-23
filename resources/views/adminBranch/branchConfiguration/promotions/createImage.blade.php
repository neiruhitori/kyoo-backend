@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <form
        action="{{ route('admin-branch.branch-configuration.promotions.image.store') }}"
        enctype="multipart/form-data"
        method="POST"
    >
        @csrf

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex">
                <a href="{{ route('admin-branch.branch-configuration.promotions.index') }}" class="btn btn-secondary mr-3">
                    <span class="fas fa-angle-left"></span>
                </a>

                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Masukkan nama promosi" />
            </div>

            <div class="d-flex align-items-center">
                <div class="mr-3 d-none" id="fileNameWrap">
                    <span class="fas fa-image mr-1"></span>
                    <span id="fileName"></span>
                </div>

                <div>
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </div>
        </div>

        @include('layouts.alert')

        <div>
            <div style="max-width: 270px; margin: 0 auto;">
                <div class="promotion-card shadow mb-3 d-flex justify-content-center align-items-center" id="promotionCard">
                    <img id="promotionImagePreview" class="promotion-img-preview" style="display: none;">

                    <div class="promotion-img-upload">
                        <label for="promotion-image">
                            <input
                                type="file"
                                accept="image/*"
                                name="promotion_img"
                                id="promotion-image"
                                onchange="handleFileChange(this)"
                                hidden
                            >
        
                            <span class="btn btn-secondary shadow">
                                <span class="fas fa-upload mr-1"></span>
                                Upload Gambar
                            </span>
                        </label>
                    </div>
                </div>

                <textarea
                    name="caption"
                    id="caption"
                    rows="2"
                    class="form-control"
                    placeholder="Tambahkan caption..."
                    maxlength="1024"
                    spellcheck="false"
                >{{ old('caption') }}</textarea>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    function handleFileChange(e) {
        if (e.files && e.files[0]) {
            const reader = new FileReader();
            reader.readAsDataURL(e.files[0]);

            $("#fileNameWrap").removeClass('d-none')
            $("#fileName").text(truncateString(e.files[0].name, 14))

            reader.onload = function (data) {
                $("#promotionImagePreview").show()
                $("#promotionImagePreview").attr('src', data.target.result)
            }
        }
    }

    function truncateString(str, num) {
        if (str.length > num) {
            return str.substring(0, num) + '...' + getFileExtension(str)
        }

        return str
    }

    function getFileExtension(str) {
        return str.split('.').pop()
    }
</script>
@endpush

@push('css')
<style>
    .promotion-card {
        border-radius: .5rem;
        height: 480px;
        width: 100%;
        background-color: #0D1117;
        position: relative;
        overflow: hidden;
    }

    .promotion-card:hover .promotion-img-upload {
        display: flex;
    }

    .promotion-img-preview {
        height: 100%;
        width: 100%;
        object-fit: contain;
    }

    .promotion-img-upload {
        background-color: rgba(13, 17, 23, .3);
        position: absolute;
        display: none;
        align-items: center;
        justify-content: center;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin-bottom: 0;
        z-index: 1;
    }
</style>
@endpush