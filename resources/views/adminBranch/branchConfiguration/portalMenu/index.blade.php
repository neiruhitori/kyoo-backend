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
        table {
             border: 1px solid #33A0FF4D; 
        }

        table th,
        table td {

            border: 1px solid #33A0FF4D !important;
        }
        table td {
            color: black
        }
    </style>
@endpush

@section('content')

@include('layouts.alert')
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
                    <div style="padding: 0rem 1rem 1rem 1rem;">
                       <ul style="">
                            <li style="margin-bottom: 0.25rem;">
                                {{ __('infobox.menuportal1') }}
                            </li>
                            <li style="margin-bottom: 0.25rem;">
                               {{ __('infobox.menuportal2') }}                        
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
</div>
<div class="accordion mb-3" id="accordionParent4">
        <div class="border-left-primary rounded " style="border-radius: 0.5rem; overflow: hidden;">

            <div  id="headingOne" style="background-color: #E6F3FF;">
                <button 
                    class="btn btn-block text-left d-flex align-items-center accordion-toggle-custom" 
                    type="button"
                    data-toggle="collapse" 
                    data-target="#accordion4" 
                    aria-expanded="true" 
                    aria-controls="accordion4"
                    style="color: #103C7C; gap: 0.5rem; outline: none; box-shadow: none; padding: 1rem;"
                    >
                        <span class="fas fa-info-circle text-primary"></span>
                            <h5 class="font-weight-bold my-0 text-primary">
                                {{ __('About Portal Menu') }}
                            </h5>
                </button>
            </div>

            <div 
                id="accordion4" 
                class="collapse show" 
                aria-labelledby="headingOne" 
                data-parent="#accordionParent4" 
                style="background-color: #E6F3FF;"
                >
                    <div style="padding: 0rem 1rem 1rem 1rem;">
                       <ul id="desc-1layer">
                            <li style="margin-bottom: 0.25rem;">
                                {{ __('This type of portal is a standard portal with 1 level/layer of pages') }}
                            </li>
                            <li style="margin-bottom: 0.25rem;">
                                {{ __('Layer 1 for selecting the type of service') }}
                        </ul>
                        <ul id="desc-2layer">
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
                    </div>
            </div>
        </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-4" style="color: #103C7C">
            Portal Menu
        </h5>
         <form action="{{ route('admin-branch.branch-configuration.menu-portal') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group ml-3">
                        <label for="select-template">{{ __('Select the appropriate layout') }}</label>
                        <select name="layer" id="selectLayer" class="form-control" onchange="changeLayout(this)">
                            <option value="1" >Portal Standard 1 Layer</option>
                            <option value="2" {{ $branchConfiguration->layer == 2 ? 'selected' : '' }}>
                                {{ Auth::user()->Branch->BranchType->is_direct_queue ? 'Portal Hybrid Onsite-Appointment 2 Layer' : '2 Layer' }}
                            </option>
                        </select>
                    </div>

                    @if(Auth::user()->Branch->BranchType->is_premium)
                    <div class="form-group ml-3" >
                        <label for="select-template">{{ __('Select the web style/theme') }}</label>
                        <select name="web_style" id="webStyle" class="form-control">
                            <option value="web-style-1" {{ $branchConfiguration->web_style == 'web-style-1' ? 'selected' : '' }}>Web Style 1</option>
                            <option value="web-style-2" {{ $branchConfiguration->web_style == 'web-style-2' ? 'selected' : '' }}>Web Style 2</option>
                            <option value="web-style-3" {{ $branchConfiguration->web_style == 'web-style-3' ? 'selected' : '' }}>Web Style 3</option>
                        </select>
                    </div>
                    @endif

                    <div class="form-group ml-3" >
                        @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
                        <div class="form-group" id="formBooking">
                            <label for="template_booking_form">{{ __('Template Booking Form') }} (Default)</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror" >
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-1" {{ $branchConfiguration->template_booking_form == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                <option value="form-medical-2" {{ $branchConfiguration->template_booking_form == 'form-medical-2' ? 'selected' : '' }}>Form Medical 2</option>
                                <option value="form-medical-3" {{ $branchConfiguration->template_booking_form == 'form-medical-3' ? 'selected' : '' }}>Form Medical 3</option>
                                <option value="form-medical-4" {{ $branchConfiguration->template_booking_form == 'form-medical-4' ? 'selected' : '' }}>Form Medical 4</option>
                                <option value="form-medical-5" {{ $branchConfiguration->template_booking_form == 'form-medical-5' ? 'selected' : '' }}>Form Medical 5</option>
                                <option value="form-financing" {{ $branchConfiguration->template_booking_form == 'form-financing' ? 'selected' : '' }}>Form Financing</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                        @endif

                        @if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_appointment)
                        <div class="form-group" id="formBookingAppointment">
                            <label for="template_booking_form">{{ __('Template Booking Form') }} (Default)</label>
                            <select name="template_booking_form" id="template_booking_form" class="form-control @error('template_booking_form') is-invalid @enderror">
                                <option value="standard-form" {{ $branchConfiguration->template_booking_form == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                <option value="form-medical-child" {{ $branchConfiguration->template_booking_form == 'form-medical-child' ? 'selected' : '' }}>Form Medical Dr Anak</option>
                            </select>
                            @include('layouts.inputError', ['errorName' => 'template_booking_form'])
                        </div>
                        @endif
                        
                    </div>


                    @if(Auth::user()->Branch->BranchType->is_premium)
                    <div class="form-group ml-3">
                        <label for="select-template">{{ __('Select the ticket style/theme') }}</label>
                        <select name="ticket_style" id="webStyle" class="form-control">
                            <option value="ticket-style-1" {{ $branchConfiguration->ticket_style == 'ticket-style-1' ? 'selected' : '' }}>Ticket Style 1</option>
                            <option value="ticket-style-2" {{ $branchConfiguration->ticket_style == 'ticket-style-2' ? 'selected' : '' }}>Ticket Style 2</option>
                            <option value="ticket-style-3" {{ $branchConfiguration->ticket_style == 'ticket-style-3' ? 'selected' : '' }}>Ticket Style 3</option>
                            <option value="ticket-style-4" {{ $branchConfiguration->ticket_style == 'ticket-style-4' ? 'selected' : '' }}>Ticket Style 4</option>
                            <option value="ticket-style-5" {{ $branchConfiguration->ticket_style == 'ticket-style-5' ? 'selected' : '' }}>Ticket Style 5</option>
                        </select>
                    </div>
                    @endif
                    <div class="form-group ml-3">
                        <button type="submit" class="btn btn-primary" style="background-color: #103C7C">{{ __('Save'). ' Form'}}</submit>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <h6 id="textLayer"></h6>
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
            </div> 
        </form>
    </div>
</div>

@if(Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
<div class="card shadow mb-4">
   <div class="card-body">
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="font-weight-bold" style="color: #103C7C">Custom Service Form</h5>
        </div>
        <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary px-4" style="background-color: #103C7C">{{ __('Save'). ' Form'}}</submit>
        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <form action="{{ route('admin-branch.branch-configuration.menu-portal.service.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <thead style="background-color:#33A0FF4D; color: #103C7C;">
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
                                        <option value="none" {{ $service->template_form_booking == null ? 'selected' : '' }}>Default</option>
                                        <option value="standard-form" {{ $service->template_form_booking == 'standard-form' ? 'selected' : '' }}>{{ __('Standard Form') }}</option>
                                        <option value="form-medical-1" {{ $service->template_form_booking == 'form-medical-1' ? 'selected' : '' }}>Form Medical 1</option>
                                        <option value="form-medical-2" {{ $service->template_form_booking == 'form-medical-2' ? 'selected' : '' }}>Form Medical 2</option>
                                        <option value="form-medical-3" {{ $service->template_form_booking == 'form-medical-3' ? 'selected' : '' }}>Form Medical 3</option>
                                        <option value="form-medical-4" {{ $service->template_form_booking == 'form-medical-4' ? 'selected' : '' }}>Form Medical 4</option>
                                        <option value="form-medical-5" {{ $service->template_form_booking == 'form-medical-5' ? 'selected' : '' }}>Form Medical 5</option>
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
        </div>
    </div>
</div>
@endif

@if(Auth::user()->Branch->BranchType->is_premium)
    <script>
           
        const oneLayer = document.getElementById('one-layer');
        const twoLayer = document.getElementById('two-layer');
        const oneDesc = document.getElementById('desc-1layer');
        const twoDesc = document.getElementById('desc-2layer');
        const textLayer = document.getElementById('textLayer');
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
                        textLayer.textContent = '1 Layer';
                    } else {
                        oneDesc.style.display = 'none';
                        twoDesc.style.display = 'block';
                        oneLayer.style.display = 'none';
                        twoLayer.style.display = 'flex';
                        formBooking.style.display = 'block';
                        textLayer.textContent = '2 Layer';
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
