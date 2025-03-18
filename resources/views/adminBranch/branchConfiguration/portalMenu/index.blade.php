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
                {{ __('Information') }}
            </h6>

            <button class="custom-muted-btn font-weight-bold text-warning" data-toggle="alert">
                {{ __('Show') }}
            </button>
        </div>

        <div class="custom-info-body">
            <p>
                <ul style="padding-left: 2rem;">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('infobox.menuportal1') }}                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('infobox.menuportal2') }}
                </ul>
            </p>
            <button class="btn btn-warning float-right" data-toggle="alert">{{ __('Hide') }}</button>
        </div>
    </div>
</div>

<div class="card mb-4 custom-info" data-open="open" role="alert">
    <div class="card-body">
        <div class="custom-info-head">
            <h6 class="font-weight-bold my-0">
                <span class="fas fa-info-circle text-primary mr-1"></span>
               {{ __('About Portal Menu') }}
            </h6>
        </div>

        <div class="custom-info-body">
            <p>
                <ul style="padding-left: 2rem;" id="desc-1layer">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('This type of portal is a standard portal with 1 level/layer of pages') }}
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('Layer 1 for selecting the type of service') }}
                </ul>
                <ul style="padding-left: 2rem;" id="desc-2layer">
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('This portal type is an onsite hybrid portal with appointments and 2 levels/layers of pages') }}
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('Layer 1 for selecting the type of service') }}
                    <li style="margin-bottom: 0.25rem;">
                        {{ __('Layer 2 for selecting the service time slot') }}
                    </li>
                    <li style="margin-bottom: 0.25rem;">
                       {{ __('The booking form for customer data can be selected according to your needs') }}
                </ul>
                
            </p>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header pb-0">
        {{-- <h6 class="font-weight-bold text-primary mb-0">
            Portal Menu
        </h6> --}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
             <a class="nav-link active px-5" id="portal-menu-tab" data-toggle="tab" href="#portal-menu" role="tab" aria-controls="portal-menu" aria-selected="true">Portal Menu</a>
            </li>
            @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
            <li class="nav-item" role="presentation">
             <a class="nav-link px-5" id="portal-service-tab" data-toggle="tab" href="#portal-service" role="tab" aria-controls="invoice" aria-selected="false">Portal Menu (Service)</a>
            </li>
            @endif
        </ul>
    </div>
    <div class="col-md-12 col-sm-6 mt-4">
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="portal-menu" role="tabpanel" aria-labelledby="portal-menu-tab">
        <form action="{{ route('admin-branch.branch-configuration.menu-portal') }}" method="POST">
            @csrf
            @method('PUT')
    
            <div class="">
                <div class="row">
                    <div class="form-group ml-3 col-md-5" style="width: 250px;">
                        <label for="select-template">{{ __('Select the appropriate layout') }}</label>
                        <select name="layer" id="selectLayer" class="form-control" onchange="changeLayout(this)">
                            <option value="1" >Portal Standard 1 Layer</option>
                            <option value="2" {{ $branchConfiguration->layer == 2 ? 'selected' : '' }}>
                                {{ Auth::user()->Branch->BranchType->is_direct_queue ? 'Portal Hybrid Onsite-Appointment 2 Layer' : '2 Layer' }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-7 d-flex" >
                        @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
                        <div class="form-group ml-3" id="formBooking">
                            <label for="template_booking_form">{{ __('Template Booking Form') }}</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror" >
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-1" {{ $branchConfiguration->template_booking_form == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                <option value="form-medical-2" {{ $branchConfiguration->template_booking_form == 'form-medical-2' ? 'selected' : '' }}>Form Medical 2</option>
                                <option value="form-financing" {{ $branchConfiguration->template_booking_form == 'form-financing' ? 'selected' : '' }}>Form Financing</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                        @endif
                        @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_appointment)
                        <div class="form-group ml-3" id="formBookingAppointment">
                            <label for="template_booking_form">{{ __('Template Booking Form') }}</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror" style="width: 240px">
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-child" {{ $branchConfiguration->template_booking_form == 'form-medical-child' ? 'selected' : '' }}>Form Medical Dr Anak</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                        @endif
                        <div class="col-md-5 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-warning ml-1 ">{{ __('Save'). ' Form'}}</submit>
                        </div>
                    </div>
            </div>
        

                <div class="d-flex mb-2 ml-2">
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
            </div>
        </form>
        </div>
    @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
        <div class="tab-pane fade mx-2 my-4" id="portal-service" role="tabpanel" aria-labelledby="portal-service-tab">
            <div class="mx-4 my-4">
                <table class="table">
                    <form action="{{ route('admin-branch.branch-configuration.menu-portal.service.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Form Template (Service)</h5>
                        <button type="submit" class="btn btn-warning ">{{ __('Save'). ' Form'}}</submit>
                    </div>
                    <thead>
                      <tr>
                        <th scope="col">Service</th>
                        <th scope="col">Template Booking Form</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                        <tr>
                             <td>{{ $service->name }}</td>
                             <td>
                                 <select name="template_form_service[{{ $service->id }}]" id="template_form_service" class="form-control" >
                                     <option value="none" {{ $service->template_form_booking == null ? 'selected' : '' }}>None</option>
                                     <option value="standard-form" {{ $service->template_form_booking == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                     <option value="form-medical-1" {{ $service->template_form_booking == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                     <option value="form-medical-2" {{ $service->template_form_booking == 'form-medical-2' ? 'selected' : '' }}>Form Medical 2</option>
                                     <option value="form-financing" {{ $service->template_form_booking == 'form-financing' ? 'selected' : '' }}>Form Financing</option>
                                 </select>
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </form>
                  </table>
            </div>
        </div>
    @endif
        </div>
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
                        formBooking.style.display = 'block';
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
