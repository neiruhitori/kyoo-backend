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

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')

@include('layouts.alert')

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="font-weight-bold text-primary mb-0">
            Portal Menu
        </h6>
    </div>

    <form action="{{ route('admin-branch.branch-configuration.menu-portal') }}" method="post">
        @csrf
        @method('put')
        <div class="card-body d-flex justify-content-center">
            <div class="d-flex gap-4">
                <div class="d-flex flex-column align-items-center cursor-pointer">
                    <label for="one-layer" class="font-weight-bolder text-dark cursor-pointer">Portal Standard 1 Layer</label>
                    <label for="one-layer" class="cursor-pointer">
                        <div class="bg-secondary mx-4 p-3">
                            <div class="bg-primary" style="width: 140px; height: 240px"></div>
                        </div>
                    </label>
                    <input type="radio" name="layer" id="one-layer" class="my-3 cursor-pointer" value="1" {{ $branchConfiguration->layer == 1 ? 'checked' : '' }}>
                </div>

                <div class="d-flex flex-column align-items-center cursor-pointer">
                    <label for="two-layer" class="ml-4 font-weight-bolder text-dark cursor-pointer">
                        {{ Auth::user()->Branch->BranchType->is_direct_queue ? 'Portal Hybrid Onsite-Appointment 2 Layer' : '2 Layer' }}
                    </label>
                    <label for="two-layer" class="cursor-pointer">
                        <div class="bg-secondary mx-4 p-3 d-flex">
                            <div class="bg-primary mx-2" style="width: 140px; height: 240px"></div>
                            <div class="bg-primary mx-2" style="width: 140px; height: 240px"></div>
                        </div>
                    </label>
                    <input type="radio" name="layer" id="two-layer" class="my-3 cursor-pointer" value="2" {{ $branchConfiguration->layer == 2 ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        <div class="wrapper-submit mr-3 mb-3">
            <button type="submit" class="btn btn-warning">Simpan</submit>
        </div>
    </form>
</div>
@endsection
