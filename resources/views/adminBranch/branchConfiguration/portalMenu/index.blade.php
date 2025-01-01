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
            /* justify-content: flex-end; */
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')

@include('layouts.alert')
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
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        Disini konfigurasi web portal antrian untuk menampilkan jenis layanan kepada pelanggan.
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        Pilih konfigurasi layout web portal yang sesuai dengan scenario proses antrian Anda.
                </ul>
            </p>
            <button class="btn btn-warning float-right" data-toggle="alert">Sembunyikan</button>
        </div>
    </div>
</div>

<div class="card mb-4 custom-info" data-open="open" role="alert">
    <div class="card-body">
        <div class="custom-info-head">
            <h6 class="font-weight-bold my-0">
                <span class="fas fa-info-circle text-primary mr-1"></span>
                Tentang Portal Menu
            </h6>
        </div>

        <div class="custom-info-body">
            <p>
                <ul style="padding-left: 2rem;" id="desc-1layer">
                    <li style="margin-bottom: 0.25rem;">
                        Jenis Portal ini merupakan portal standar dengan 1 tingkatan/layer halaman
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        Layer ke-1 untuk pemilihan jenis layanan
                </ul>
                <ul style="padding-left: 2rem;" id="desc-2layer">
                    <li style="margin-bottom: 0.25rem;">
                        Jenis Portal ini merupakan portal onsite hybrid dengan appointment dengan 2 tingkatan/layer halaman 
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        Layer ke-1 untuk pemilihan jenis layanan 
                    <li style="margin-bottom: 0.25rem;">
                        Layer ke-2 untuk pemilihan slot waktu layanan
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        Form Booking untuk data pelanggan bisa dipilih sesuai dengan kebutuhan anda

                </ul>
                
            </p>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="font-weight-bold text-primary mb-0">
            Portal Menu
        </h6>
    </div>
    <div class="col-md-12 col-sm-12 mt-3">
        <form action="{{ route('admin-branch.branch-configuration.menu-portal') }}" method="POST">
            @csrf
            @method('PUT')
    
            <div class="">
                <div class="row">
                 
                    <div class="form-group ml-3 col-md-5" style="width: 250px;">
                        <label for="select-template">Pilih layout yang sesuai</label>
                        <select name="layer" id="selectLayer" class="form-control" onchange="changeLayout(this)">
                            <option value="1" >Portal Standard 1 Layer</option>
                            <option value="2" {{ $branchConfiguration->layer == 2 ? 'selected' : '' }}>
                                {{ Auth::user()->Branch->BranchType->is_direct_queue ? 'Portal Hybrid Onsite-Appointment 2 Layer' : '2 Layer' }}
                            </option>
                        </select>
                    </div>
                    @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_appointment)
                    <div class="col-md" id="formBookingAppointment">
                        <div class="form-group">
                            <label for="template_booking_form">{{ __('Template Booking Form') }}</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror" style="width: 240px">
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-child" {{ $branchConfiguration->template_booking_form == 'form-medical-child' ? 'selected' : '' }}>Form Medical Dr Anak</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
                    <div class="col-md" id="formBooking">
                        <div class="form-group">
                            <label for="template_booking_form">{{ __('Template Booking Form') }}</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror" >
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-1" {{ $branchConfiguration->template_booking_form == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                <option value="form-financing" {{ $branchConfiguration->template_booking_form == 'form-financing' ? 'selected' : '' }}>Form Financing</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                    </div>
                    @endif
                  
        </div>

                <div class="d-flex mb-2 ml-3">
                    <div class="d-flex flex-column align-items-center" >
                        <label for="one-layer" class="bg-secondary mx-2 p-2 rounded" id="one-layer">
                            <img src="{{ asset('img/portal-menu/1-layer.jpeg') }}" style="width: 175px; height: 295px" alt="">
                            {{-- <div class="bg-secondary mx-4 p-3">
                                <div class="bg-primary" style="width: 140px; height: 240px"></div>
                            </div> --}}
                        </label>
                    </div>
    
                    <div class="d-flex flex-column align-items-center">
                        <label for="two-layer" class="twoLayer bg-secondary mx-2 p-2 rounded" id="two-layer">
                            <img src="{{ asset('img/portal-menu/2-layer.jpeg') }}" style="width: 355px; height: 325px" alt="">
                            {{-- <div class="bg-secondary mx-4 p-3 d-flex">
                                <div class="bg-primary mx-2" style="width: 140px; height: 240px"></div>
                                <div class="bg-primary mx-2" style="width: 140px; height: 240px"></div>
                            </div> --}}
                        </label>
                    </div>
                   
                </div>
               

                <div class="col-md-7 ml-2 mb-3">
                    <button type="submit" class="btn btn-warning ml-1 ">Simpan</submit>
                </div>
            </div>
        </form>
    </div>
   
</div>

@if(Auth::user()->Branch->BranchType->is_premium)
    <script>
           
        const oneLayer = document.getElementById('one-layer');
        const twoLayer = document.getElementById('two-layer');
        const oneDesc = document.getElementById('desc-1layer');
        const twoDesc = document.getElementById('desc-2layer');
        const formBooking = document.getElementById('formBooking');
        oneDesc.style.display = 'none';
        twoDesc.style.display = 'none';
        oneLayer.style.display = 'none';
        twoLayer.style.display = 'none';
        
            function changeLayout(input) {

                    const { value } = input;
                    if (value == '1') {
                        oneDesc.style.display = 'block';
                        twoDesc.style.display = 'none';
                        oneLayer.style.display = 'flex';
                        twoLayer.style.display = 'none';
                        formBooking.style.display = 'none';
                    } else {
                        oneDesc.style.display = 'none';
                        twoDesc.style.display = 'block';
                        oneLayer.style.display = 'none';
                        twoLayer.style.display = 'flex';
                        formBooking.style.display = 'flex';
                    }
                }
                window.onload = function() {
                    var selectTemplate = document.getElementById('selectLayer');
                    let temVal = selectTemplate.value;
                    changeLayout(temVal);
                };
        document.addEventListener("DOMContentLoaded", function() {
           
           

            // oneLayer.addEventListener('change', function() {
            //     if (this.checked) {
            //         formBooking.classList.add('d-none');
            //     }
            // });

            // twoLayer.addEventListener('change', function() {
            //     if (this.checked) {
            //         formBooking.classList.remove('d-none');
            //     }
            // });

            
        });
    </script>
@endif
@endsection
