@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <form
        action="{{ route('admin-branch.branch-configuration.promotions.text.store') }}"
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
                <input type="hidden" name="color">
                <input type="hidden" name="font_size">

                <button type="button" class="btn btn-secondary mr-3" onclick="handleColorClick(this)">
                    <span class="fas fa-palette"></span>
                </button>

                <div>
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </div>
        </div>

        @include('layouts.alert')

        <div>
            <div style="max-width: 270px; margin: 0 auto;">
                <div
                    class="promotion-card shadow mb-3 d-flex justify-content-center align-items-center"
                    id="promotionCard"
                >
                    <textarea
                        class="promotion-text"
                        spellcheck="false"
                        maxlength="700"
                        oninput="handleTextInput(this)"
                        name="text"
                        placeholder="Type a promotion"
                        autofocus
                    >{{ old('text') }}</textarea>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    let i = 0
    const COLORS = [
        '#7a203b', '#c3a040', '#8fa843', '#a62a73',
        '#8493ca', '#233540', '#7ac9a6', '#803e8e',
        '#5796ff', '#7f8fa6', '#74676a', '#58c9ff',
        '#26c2de', '#ff7a6d', '#57c266', '#ff8a8c',
        '#8d6893', '#c79ecd', '#b8b226', '#f0b230',
        '#ae8775'
    ]
    const DEFAULT_FONT = '1.125em'
    const SMALL_FONT = '.65em'

    $(document).ready(function () {
        setColorInput(COLORS[0])
        setFontSizeInput(DEFAULT_FONT)
    })

    function handleColorClick(e) {
        i++
        if (i >= COLORS.length) i = 0

        setPromotionCardColor(COLORS[i])
        setColorInput(COLORS[i])
    }

    function handleTextInput(e) {
        e.style.fontSize = DEFAULT_FONT
        if (e.value.length > 105) {
            e.style.fontSize = SMALL_FONT
        }

        setFontSizeInput(e.style.fontSize)
 
        e.style.height = `32px`
        e.style.height = `${e.scrollHeight}px`
    }

    function setColorInput(color) {
        $("input[name='color']").val(color)
    }

    function setFontSizeInput(fontSize) {
        $("input[name='font_size']").val(fontSize)
    }

    function setPromotionCardColor(color) {
        $("#promotionCard").css('background-color', color)
    }
</script>
@endpush

@push('css')
<style>
    .promotion-card {
        border-radius: .5rem;
        height: 480px;
        width: 100%;
        background-color: #7a203b;
        position: relative;
        padding: 10px;
    }

    .promotion-card:hover .promotion-img-upload {
        display: flex;
    }

    .promotion-text {
        display: block;
        color: white;
        background: transparent;
        border: none;
        outline: none;
        max-height: 420px;
        width: 100%;;
        height: 32px;
        font-size: 1.125em;
        overflow: hidden;
        resize: none;
        text-align: center;
    }
</style>
@endpush