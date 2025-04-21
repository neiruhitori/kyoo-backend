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
        input[type=radio]{
  transform:scale(1.5);
}

.badge-stroke {
        text-shadow: 
            -1px -1px 0 #000,  
             1px -1px 0 #000,
            -1px  1px 0 #000,
             1px  1px 0 #000;
    }
    </style>
@endpush

@section('content')

@include('layouts.alert')



    <div class="mb-3 mx-3">
        <h5 class="font-weight-bold text-primary mb-0">
           {{ __('Billing Menu') }}
        </h5>
    </div>

    @if ($unpaidInvoice)

    <div class="card text-white bg-info mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">{{ __('Invoice Notification') }}</h5>
            <div class="row">
                <div class="col mb-2">
                    <div>{{ __('Paid Amount') }}:</div> 
                    <b>{{ $unpaidInvoice->currency ?? 'Rp' }}. {{ number_format($unpaidInvoice->amount, 2, ',', '.') }}</b>
                </div>
                <div class="col mb-2">
                    <div>{{ __('Expires On') }}: </div>
                    <b>{{ \Carbon\Carbon::parse($unpaidInvoice->expiry_date)->translatedFormat('d F Y H:i')}}</b>
                </div>
                <div class="col mb-2">
                    <div>{{ __('Payment Status') }}: </div>
                    <b id="unpaidStatus"><span class="badge badge-warning text-dark" >{{ $unpaidInvoice->status}}</span></b>
                </div>
                <div class="col mb-2">
                    <a href="{{ $unpaidInvoice->invoice_url}}" target="_blank" class="btn btn-primary py-2 px-3" id="payButton">{{ __('Pay Here') }}</a>
                </div>
            </div>
        </div>
      </div>
     
    @endif
    

<div class="card shadow mb-4">
    <div class="card-body">     
        <form action="" id="formInvoice" method="POST">
            @csrf
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="license" role="tabpanel" aria-labelledby="license-tab">

                    <div class="mx-4 my-4">
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('License Type') }}:</h6>
                            <div class="d-flex">
                                <select class="custom-select" id="packageSelection" name="packageSelection" style="max-width: 300px;"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                                    <option value="lite" {{ $subscription && $subscription->package == 'lite' ? 'selected' : '' }}>Lite</option>
                                    <option value="premium" {{ $subscription && $subscription->package == 'premium' ? 'selected' : '' }}>Premium</option>
                                    @if (Auth::user()->Branch->country == 'Indonesia')
                                    <option value="custom" {{ $subscription && $subscription->package == 'custom' ? 'selected' : '' }}>Custom</option>
                                    @else
                                        
                                    @endif
                                </select>
                            </div>
                        </div> 
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('Queue Type') }}:</h6>

                           @if ($isDirect)
                           <input style="max-width: 200px;" type="text" class="form-control" id="license_input" value="Onsite" readonly>
                           <input type="hidden" name="license_type" id="license_type" value="onsite">
                           @else
                           <input type="hidden" name="license_type" id="license_type" value="appointment">
                           <input style="max-width: 200px;" type="text" class="form-control" id="license_input" value="Appointment" readonly>
                           @endif
                        </div>
                        <div class="d-flex align-items-start mb-1">
                            <h6 style="min-width: 150px;" class="pt-2">{{ __('Subs. Duration') }}:</h6>
                            <select class="custom-select" style="max-width: 200px;" name="subs_duration" id="subs_duration"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                                <option value="3" {{ $subscription && $subscription->subs_duration == '3' ? 'selected' : '' }}>3</option>
                                <option value="6" {{ $subscription && $subscription->subs_duration == '6' ? 'selected' : '' }}>6</option>
                                <option value="12" {{ $subscription && $subscription->subs_duration == '12' ? 'selected' : '' }}>12</option>
                              </select>
                              <p class="pt-2 ml-3">{{ __('Month') }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('Maximum Queue') }}:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="queue" id="queue" min="100" max="500" required value="{{ $subscription ? $subscription->queue  : '100' }}" readonly>
                            <p class="pt-2 ml-3">{{ __('Queue Per Day') }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('Counter Amount') }}:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="table" id="table"  min="1" max="5" required value="{{ $subscription ? $subscription->max_table  : '1' }}" readonly>
                            <p class="pt-2 ml-3">{{ __('Workstation') }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('Staff') }}:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="services" id="services" min="1" max="15" required value="{{ $subscription ? $subscription->max_service  : '1' }}" readonly>
                            <p class="pt-2 ml-3">{{ __('Staff') }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div id="web-kiosk" style="display: flex">
                            <h6 style="min-width: 150px;" class="pt-1">Web Kiosk:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="kiosk" id="kiosk" min="0" max="3" required value="{{ $subscription ? $subscription->kiosk  : '0' }}" readonly>
                            <p class="pt-2 ml-3">{{ __('Device') }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-5">
                            <div id="web-signage" style="display: flex">
                                <h6 style="min-width: 150px;" class="pt-1">Web Signage TV:</h6>
                                <input style="max-width: 200px;" type="number" class="form-control" name="signage" id="signage" min="1" max="3" value="1" required readonly>
                                <p class="pt-2 ml-3">{{ __('Device') }}</p>
                            </div>
                        </div>
                        @if (Auth::user()->Branch->country == 'Indonesia')
                        <div class="d-flex align-items-center mb-5">
                            <h6 style="min-width: 150px;" class="pt-1">{{ __('Feature') }}:</h6>
                            <div id="feature">
                                <b></b>
                            </div>
                        </div>
                        @endif
                        
                        <input type="hidden" name="amount" id="amount">
                       
                    </div>
                    <div class="d-flex align-items-center ml-4 mb-2">
                        @if ($unpaidInvoice)
                        <button class="btn btn-primary px-5" disabled>{{ __('Complete Payment First') }}</button>
                        @else
                        <button class="btn btn-primary px-5" id="modalBtn" type="button" type="button" data-toggle="modal" data-target="#staticBackdrop" >{{ __('Continue') }}</button>
                        @endif
                    </div>
                    
                </div>


            </div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-dark" id="staticBackdropLabel"><b>{{ __('KYOO Subscription') }}</b></h5>
        </div>
        <div class="modal-body">
          <div class="row " style="color: #000">
            <div class="col-md-3">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>{{ __('License Type') }}:</b></h6>
                </div>
            </div>
            <div class="col-md-9">
                <div class="ml-2" style="min-width: 200px">
                    <h6 class="" id="md_license"></h6>
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>{{ __('Queue Type') }}:</b></h6>
                </div>
            </div>
            <div class="col-md-9">
                <div class="ml-2" style="min-width: 200px">
                    <h6 class="" id="md_queue_type"></h6>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>{{ __('Subs. Duration') }}:</b></h6>
                </div>
            </div>
            <div class="col-md-9">
                <div  class="ml-2" style="min-width: 200px">
                    <h6 class="" id="md_subsDuration"></h6>
                </div>
            </div>

        <div class="col-md-3 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>{{ __('Max Queue') }}:</b></h6>
            </div>
        </div>    
        <div class="col-md-9">
            <div class="ml-2" style="min-width: 90px">
                <h6 class="" id="md_queue"></h6>
            </div>
        </div>

        <div class="col-md-3" id="md_table_container">
            <div class="d-flex align-items-center mb-2" >
                <h6 class="mr-2" style="min-width: 110px"><b>{{ __('Counter') }}:</b></h6>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ml-2" style="min-width: 90px">
                <h6 class="" id="md_table"></h6>
            </div>    
        </div>

        <div class="w-100"></div>

        <div class="col-md-3 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>{{ __('Staff') }}:</b></h6>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ml-2" style="min-width: 90px">
                <h6 class="" id="md_service"></h6>
            </div>
        </div>

        <div class="col-md-3" id="md_kiosk_container">
            <div class="d-flex align-items-center mb-2" >
                <h6 class="mr-2" style="min-width: 110px"><b>Web Kiosk:</b></h6>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ml-2" style="min-width: 90px">
                <h6 class="" id="md_kiosk"></h6>
            </div>
        </div>

        <div class="w-100"></div>

        <div class="col-md-3 " id="md_signage_container">
            <div class="d-flex align-items-end mb-2" >
                <h6 class="mr-2" style="min-width: 112px"><b>Web Signage:</b></h6>
            </div>
        </div>
        <div class="col-md-9">
            <div id="signage" class="ml-2" style="min-width: 90px">
                <h6 class="" id="md_signage"></h6>
            </div>
        </div>

        <div class="col-md-12" id="no_license_data">
            <h5><span class="badge badge-danger">{{ __('License Not Available') }}</span></h5>
        </div>

          </div>

          <hr style="border-color:#000;">

          <div class="row mx-1" style="color: #000">
            <table class="table" style="color: #000" id="itemContainer">
                <thead>
                  <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Period') }}</th>
                    <th>{{ __('Item Price') }}</th>
                    <th>{{ __('Total Price') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{{ __('Workstation') }}</td>
                    <td id="tableQty"></td>
                    <td class="itemPeriod"></td>
                    <td id="tablePrice"></td>
                    <td id="tableTotalPrice"></td>
                  </tr>
                  <tr id="customSignageContainer">
                    <td>Web Signage</td>
                    <td id="signageQty"></td>
                    <td class="itemPeriod"></td>
                    <td id="signagePrice"></td>
                    <td id="signageTotalPrice"></td>
                  </tr>
                  <tr id="customKioskContainer">
                    <td>Web Kiosk</td>
                    <td id="kioskQty"></td>
                    <td class="itemPeriod"></td>
                    <td id="kioskPrice"></td>
                    <td id="kioskTotalPrice"></td>
                  </tr>
                  <tr>
                    <th colspan="4" class="text-right ">Subtotal Item :</th>
                    <td id="items"></td>
                  </tr>
                  {{-- <tr>
                    <th colspan="4" class="text-right ">Harga Lisensi:</th>
                    <td id="customLicensePrice"></td>
                  </tr> --}}
                  <tr>
                    <th colspan="4" class="text-right ">{{ __('VAT 11%') }} :</th>
                    <td id="customTax"></td>
                  </tr>
                  <tr>
                    <th colspan="4" class="text-right "><h5><b>TOTAL:</b></h5></th>
                    <td id="customTotal"></td>
                  </tr>
                </tbody>
              </table>

          </div>

          <div class="row" style="color: #000" id="nonCustom">

            <div class="col-md-12" id="pricepercounter">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Counter Price /Month:</b></h6>
                        <div id="pingu" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
            <div class="col-md-12" id="summCounter">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Counter Price:</b></h6>
                        <div id="zafkiel" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
            <div class="col-md-12" id="signagePriceOver">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Signage Price:</b></h6>
                        <div id="raphiel" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
            <div class="col-md-12" id="kioskPriceOver">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Kiosk Price:</b></h6>
                        <div id="miriel" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>

            <div class="col-md-12 d-flex">
                <div class="d-flex mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Subtotal Item:</b></h6>
                        <div id="price" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>

            <div class="col-md-12" id="formTax">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>{{ __('VAT 11%') }}  :</b></h6>
                        <div id="tax" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
            
           
            <div class="col-md-12 d-flex">
                <div class="d-flex align-items-center mb-2">
                    <h5 class="mr-2" style="min-width: 112px"><b>TOTAL :</b></h5>
                        <div id="total" class="ml-2" style="min-width: 90px">
                            
                            <h5 class=""></h5>
                        </div>
                </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Cancel') }} </button>
          <button type="submit" id="confirmBtn" class="btn btn-primary">{{ __('Continue Payment') }} </button>
        </div>
      </div>
    </div>
  </div>


        </form>
    </div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    let feature = document.getElementById('feature');
    let queue = document.getElementById('queue');
    let table = document.getElementById('table');
    let services = document.getElementById('services');
    let kiosk = document.getElementById('kiosk');
    let signage = document.getElementById('signage');
    let subsDuration = document.getElementById('subs_duration'); 
    let packageSelection = document.getElementById('packageSelection'); 
    let form = document.getElementById('formInvoice');
    let queueType = document.getElementById('license_type');
    let modalButton = document.getElementById('modalBtn');
    let signageContainer = document.getElementById('web-signage');
    let kioskContainer = document.getElementById('web-kiosk');
    let modalSignage = document.getElementById('md_signage_container');
    let priceElement = document.getElementById('price');
    let taxElement = document.getElementById('tax');
    let itemsElement = document.getElementById('items');
    let totalElement = document.getElementById('total');
    let amount = document.getElementById('amount');
    let payButton = document.getElementById("payButton");
    let unpaidStatus = document.getElementById("unpaidStatus");

// function toggleSignageInput(selectedPackage) {
//     const isDirect = {!! json_encode($isDirect) !!};//blade escape
//     updateFeatures(selectedPackage, isDirect);
//     if (selectedPackage === 'premium') {
//         queue.setAttribute('readonly', true);
//         table.setAttribute('readonly', true);
//         services.setAttribute('readonly', true);
//         kiosk.setAttribute('readonly', true);

//         queue.value = 500;
//         table.value = 1;
//         services.value = 3;
//         kiosk.value = 0;
//         signageContainer.style.display = 'flex';  
//         modalSignage.style.display = 'flex';
//         signage.setAttribute('readonly', true);   
//         signage.value = 1; // Nilai default saat premium
//     } else if (selectedPackage === 'custom') {
//         // queue.removeAttribute('readonly');
//         table.removeAttribute('readonly');
//         services.removeAttribute('readonly');
//         kiosk.removeAttribute('readonly');
        
//         table.addEventListener('input', function() {
//         const tableValue = parseInt(table.value) || 0;
//         let country = "{{ Auth::user()->Branch->country }}"
//         if(country == 'Indonesia'){
//             services.value = tableValue * 2;
//         }else{
//             services.value = tableValue * 2;
//         }

//                 if (tableValue >= 2) {
//                     queue.value = 500
//                 }else{
//                     queue.value = 100
//                 }
//             });
//         signageContainer.style.display = 'flex';
//         modalSignage.style.display = 'flex';  
//         signage.removeAttribute('readonly');  
//         signage.value = 1;     
//         kiosk.value = 1;
//     } else {
//         queue.setAttribute('readonly', true);
//         table.setAttribute('readonly', true);
//         services.setAttribute('readonly', true);
//         kiosk.setAttribute('readonly', true);
//         queue.value = 100;
//         table.value = 1;
//         services.value = 1;
//         kiosk.value = 0;
//         signageContainer.style.display = 'none';
//         modalSignage.style.display = 'none';  
//         signage.value = '';                      
//     }
//     updateFeatures(selectedPackage, isDirect);
                      
// }

// queue.addEventListener('input',function() {
//     let value = parseInt(this.value) || 1;
//     let package = packageSelection.value;

//     if (package == 'lite' && value > 100) {
//         this.value = Math.min(Math.max(value, 1), 100);
//     }
// })



function checkExpiry() {
    if("{{ $unpaidInvoice }}" !== ""){
        let expiryDate = new Date("{{ $unpaidInvoice->expiry_date ?? '' }}");
        let now = new Date();
        if (now >= expiryDate) {
            unpaidStatus.innerHTML = `<span class="badge badge-danger text-white" >EXPIRED</span>`
            payButton.style.pointerEvents = "none"; 
            payButton.style.opacity = "0.5"; 
            payButton.innerText = "Expired"; 
        }
    }else{

    }
}
if("{{ $unpaidInvoice }}" !== ""){
checkExpiry(); 
setInterval(checkExpiry, 24 * 60 * 60 * 1000);
}

limitInput(queue,100,500,packageSelection);
limitInput(table,1,5,packageSelection);
limitInput(services,1,15,packageSelection);
limitInput(kiosk,1,3,packageSelection);
limitInput(signage,1,3,packageSelection);

function limitInput(element, min, max, packageSelection = null) {
    element.addEventListener('input', function () {
        let value = parseInt(this.value) || min; // Default ke `min` jika tidak valid
        if(element.id == 'queue'){
            if (packageSelection) {
                max = (packageSelection.value === 'lite') ? 100 : 500; 
            }
        }
        this.value = Math.min(Math.max(value, min), max);
    });
}

function toggleSignageInput(selectedPackage) {
    const isDirect = {!! json_encode($isDirect) !!}; // Blade escape
    const country = "{{ Auth::user()->Branch->country }}";
    //non-indo
    if (country !== 'Indonesia') {
        queue.removeAttribute('readonly');
        table.removeAttribute('readonly');
        services.removeAttribute('readonly');
        kiosk.removeAttribute('readonly');

        signageContainer.style.display = 'flex';
        modalSignage.style.display = 'flex';
        
        if (selectedPackage === 'premium') {
            queue.removeAttribute('readonly');
            signage.removeAttribute('readonly');
                    setValues({
                        queue: 500,
                        table: 1,
                        services: 2,
                        kiosk: 0,
                        signage: 1
                    });
                    signageContainer.style.display = 'flex';
                    kioskContainer.style.display = 'flex';
                    modalSignage.style.display = 'flex';
                }else {
                    setReadOnly([queue, services, kiosk, signage], true);
                    table.addEventListener('input', function () {
                    const tableValue = parseInt(table.value) || 0;
                    services.value = tableValue * 2;
                });
                setValues({
                    queue: 100,
                    table: 1,
                    services: 2,
                    kiosk: 0,
                    signage: ''
                });
                signageContainer.style.display = 'none';
                kioskContainer.style.display = 'none';
                modalSignage.style.display = 'none';
            }
        return;

    }


    //indo
    if (selectedPackage === 'premium') {
        updateFeatures(selectedPackage, isDirect);
        setReadOnly([queue, table, services, kiosk, signage], true);
        setValues({
            queue: 500,
            table: 1,
            services: 3,
            kiosk: 0,
            signage: 1
        });
        signageContainer.style.display = 'flex';
        modalSignage.style.display = 'flex';
    } else if (selectedPackage === 'custom') {
        table.removeAttribute('readonly');
        services.removeAttribute('readonly');
        kiosk.removeAttribute('readonly');
        signage.removeAttribute('readonly');

        table.addEventListener('input', function () {
            const tableValue = parseInt(table.value) || 0;
            services.value = tableValue * 3;
            queue.value = tableValue >= 2 ? 500 : 100;
        });

        signageContainer.style.display = 'flex';
        modalSignage.style.display = 'flex';
        setValues({ signage: 1, kiosk: 1 });
    } else {
        setReadOnly([queue, table, services, kiosk], true);
        setValues({
            queue: 100,
            table: 1,
            services: 1,
            kiosk: 0,
            signage: ''
        });
        signageContainer.style.display = 'none';
        modalSignage.style.display = 'none';
    }

    updateFeatures(selectedPackage, isDirect);
}

function setReadOnly(elements, state) {
    elements.forEach(el => el.setAttribute('readonly', state));
}

function setValues(values) {
    Object.keys(values).forEach(id => {
        if (document.getElementById(id)) {
            document.getElementById(id).value = values[id];
        }
    });
}




function updateFeatures(selectedPackage, isDirect) {
    let featuresText = '';

    if (selectedPackage === 'lite') {
        featuresText = isDirect 
            ? `{{ __('Linktree from Webtokoo') }}` 
            : `{{ __('Email Notification and Linktree') }}`;
    } else if (selectedPackage === 'premium') {
        featuresText = isDirect 
            ? `{{ __('Voice Calls, TV Monitoring, Hybrid Appointment Queue, Web Survey, and Linktree') }}`
            : `{{ __('Email, WA Notification, and Linktree') }}`;
    } else if (selectedPackage === 'custom') {
        featuresText = isDirect 
            ? `{{ __('Voice Calls, TV Monitoring, Hybrid Appointment Queue, Web Survey, and Linktree') }}`
            : `{{ __('Email, WA Notification, and Linktree') }}`;
    }

    feature.innerHTML = `<b>${featuresText}</b>`;
}


function getModalData() {
    let tableVal = table.value;
    let queueVal = queue.value;
    let serviceVal = services.value;
    let packageVal = '';
    let subsDurationVal = subsDuration.value;
    let queueTypeVal = '';
        document.getElementById('md_table_container').style.display = 'flex';
        document.getElementById('md_kiosk_container').style.display = 'none';
        document.getElementById('md_signage_container').style.display = 'none';

    if(packageSelection.value === "custom"){
        let kioskVal = kiosk.value;
        let signageVal = signage.value;
        packageVal = "Custom";
        document.getElementById('md_kiosk').innerHTML = kioskVal + ` {{ __('Device') }}`;
        document.getElementById('md_signage').innerHTML = signageVal + ` {{ __('Device') }}`;
        // document.getElementById('md_table_container').style.display = 'flex';
        document.getElementById('md_kiosk_container').style.display = 'flex';
        document.getElementById('md_signage_container').style.display = 'flex';
        
    }else if(packageSelection.value === "premium"){
        let signageVal = signage.value;
        let kioskVal = kiosk.value;        
        packageVal = "Premium";
        document.getElementById('md_signage_container').style.display = 'flex';
        document.getElementById('md_kiosk_container').style.display = 'flex';
        document.getElementById('md_signage').innerHTML = signageVal + ` {{ __('Device') }}`;
        document.getElementById('md_kiosk').innerHTML = kioskVal + ` {{ __('Device') }}`;
    }else{
        packageVal = "Lite";
    }
    //tipe antrian
    if(queueType.value == "onsite"){
        queueTypeVal = "Onsite";
    }else{
        queueTypeVal = "Appointment";
    }
    // document.getElementById('md_kiosk').innerHTML = kioskVal + ` {{ __('Device') }}`;
    document.getElementById('md_table').innerHTML = tableVal + ` {{ __('Workstation') }}`;
    document.getElementById('md_license').innerHTML = packageVal;
    document.getElementById('md_queue_type').innerHTML = queueTypeVal;
    document.getElementById('md_subsDuration').innerHTML = subsDurationVal + ` {{ __('Month') }}`;
    document.getElementById('md_queue').innerHTML = queueVal + ` {{ __('Queue') }}`;
    document.getElementById('md_service').innerHTML = serviceVal + ` {{ __('Staff') }}`;
    
}

subsDuration.addEventListener('change', function() {
    const selectedPackage = packageSelection.value;
});

packageSelection.addEventListener('change', function() {
    const selectedPackage = packageSelection.value;
    toggleSignageInput(selectedPackage);
});


function calculateTotal(price, country = 'Indonesia') {
    let tax = 0;
    if (country == 'Indonesia') {
        tax = price * 0.11; // PPN 11%
    }
    const total = price + tax;
    return {price, tax, total}
}

function getData(selectedPackage){
    const selectedDuration = parseInt(subsDuration.value);
    let noDataBadge = document.getElementById('no_license_data');
    
        let license = selectedPackage;

        let tableVal = table.value;
        let kioskVal = kiosk.value;
        let signageVal = signage.value;

    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Loading...';
    noDataBadge.style.display = 'none';
    document.getElementById('itemContainer').style.display = 'none';
    document.getElementById('nonCustom').style.display = 'none';

    $.ajax({
    url: '/admin-branch/get_Billing_Prices', 
    method: 'GET',
    data:{
        'duration':selectedDuration,
        'license':selectedPackage,
        'table_qty': tableVal,
        'kiosk_qty': kioskVal,
        'signage_qty': signageVal,
    },
    success: function(response) {
        if (response.status === 200) {
            const data = response.data;
            
            if (data) {
                const { price, tax, total, itemPrices } = calculateTotal(data.license_prices, data.country);
              
                if(data.billing_type == 'custom'){
                    //jika license custom
                    document.getElementById('itemContainer').style.display = 'table';
                    const period = document.querySelectorAll('.itemPeriod');
                    period.forEach(item => {
                        item.innerHTML = `<h6>${selectedDuration} {{ __('Month') }}</h6>`;
                    });

                    let custKioskContainer = document.getElementById('customKioskContainer');
                    custKioskContainer.style.display = 'revert';
                    let custSignageContainer= document.getElementById('customSignageContainer');
                    custSignageContainer.style.display = 'revert';

                    if(kiosk.value == 0){
                        custKioskContainer.style.display = 'none';
                    }
                    if(signage.value == 0){
                        custSignageContainer.style.display = 'none';
                    }

                    let tablePrice = data.total_table_prices / table.value / selectedDuration;
                    let kioskPrice = data.total_kiosk_prices / kiosk.value / selectedDuration;
                    let signagePrice = data.signage_prices / signage.value / selectedDuration;
                    signagePrice = isNaN(signagePrice) ? 0 : signagePrice
                    kioskPrice = isNaN(kioskPrice) ? 0 : kioskPrice;

                    document.getElementById('tableQty').innerHTML = `<h6>${table.value}</h6>`;
                    document.getElementById('tablePrice').innerHTML = `<h6>${tablePrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })} /{{ __('Month') }}</h6>`;
                    document.getElementById('tableTotalPrice').innerHTML = `<h6>${data.total_table_prices.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;
                    document.getElementById('kioskQty').innerHTML = `<h6>${kiosk.value}</h6>`;
                    document.getElementById('kioskPrice').innerHTML = `<h6>${kioskPrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })} /{{ __('Month') }}</h6>`;
                    document.getElementById('kioskTotalPrice').innerHTML = `<h6>${data.total_kiosk_prices.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;
                    document.getElementById('signageQty').innerHTML = `<h6>${signage.value}</h6>`;
                    document.getElementById('signagePrice').innerHTML = `<h6>${signagePrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })} /{{ __('Month') }}</h6>`;
                    document.getElementById('signageTotalPrice').innerHTML = `<h6>${data.signage_prices.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;
                    // document.getElementById('customLicensePrice').innerHTML = `<h6>${price.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;

                    document.getElementById('customTax').innerHTML = `<h6>${tax.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;

                    document.getElementById('customTotal').innerHTML = `<h5><b>${total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h5>`;
                    itemsElement.innerHTML = `<h6><b>${data.license_prices.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h6>`;
                }else{
                    let custTaxContainer= document.getElementById('formTax');
                    let custPerContainer= document.getElementById('pricepercounter');
                    let sigOverContainer= document.getElementById('signagePriceOver');
                    let summContainer= document.getElementById('summCounter');
                    let kiOverContainer= document.getElementById('kioskPriceOver');
                    custTaxContainer.style.display = 'revert';
                    custPerContainer.style.display = 'none';
                    kiOverContainer.style.display = 'none';
                    sigOverContainer.style.display = 'none';
                    summContainer.style.display = 'none';
                    //if non_idn
                    document.getElementById('nonCustom').style.display = 'flex';
                    if(data.country != 'Indonesia'){
                        custTaxContainer.style.display = 'none';
                            custPerContainer.style.display = 'revert';
                            let perCounterPrice = price/data.subscription_duration/table.value;

                        if (data.billing_type != 'lite') {
                            
                            let kioskPrice = data.total_kiosk_prices / kiosk.value / selectedDuration;
                            let signagePrice = data.signage_prices / signage.value / selectedDuration;
                            signagePrice = isNaN(signagePrice) ? 0 : signagePrice
                            kioskPrice = isNaN(kioskPrice) ? 0 : kioskPrice;
                            let finalPrice = total + data.total_kiosk_prices + data.signage_prices;

                            kiOverContainer.style.display = 'revert';
                            sigOverContainer.style.display = 'revert';
                            summContainer.style.display = 'revert';
                            document.getElementById('zafkiel').innerHTML = 
                            `<h6>${tableVal} Counter x 
                                 ${data.subscription_duration} Months x 
                                USD ${perCounterPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}
                                </h6>`;
                            document.getElementById('miriel').innerHTML = 
                            `<h6>
                                ${kiosk.value} Device
                                ${kiosk.value > 0 ? 
                                ` x ${data.subscription_duration} Months x USD ${kioskPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}` 
                                : ''}
                            </h6>`;
                            document.getElementById('raphiel').innerHTML = 
                            `<h6>
                                ${signage.value} Device
                                ${signage.value > 0 ? 
                                ` x ${data.subscription_duration} Months x USD ${signagePrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}` 
                                : ''}
                            </h6>`;
                            document.getElementById('pingu').innerHTML =
                            `<h6>USD ${perCounterPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</h6>`;
                            priceElement.innerHTML = 
                            `<h6>Counter : USD ${total.toLocaleString('en-US', { style: 'currency', currency: 'USD' })} </h6>
                             <h6>Signage : USD ${data.signage_prices.toLocaleString('en-US', { style: 'currency', currency: 'USD' })} </h6>
                             <h6>Kiosk : USD ${data.total_kiosk_prices.toLocaleString('en-US', { style: 'currency', currency: 'USD' })} </h6>`;
                            totalElement.innerHTML = `<h5><b>USD ${finalPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</b></h5>`;
                            amount.value = finalPrice;
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = `{{ __('Continue Payment') }}`;
                            noDataBadge.style.display = 'none';
                            return
                        }else{
                          
                            // let perCounterPrice = data.total_table_prices / table.value / selectedDuration;
                            document.getElementById('pingu').innerHTML =
                            `<h6>USD ${perCounterPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</h6>`;
                            priceElement.innerHTML = `<h6>${tableVal} Counter x ${data.subscription_duration} Months x USD ${perCounterPrice.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</h6>`;
                            totalElement.innerHTML = `<h5><b>USD ${total.toLocaleString('en-US', { style: 'currency', currency: 'USD' })}</b></h5>`;
                        }
                    }else{
                        priceElement.innerHTML = `<h6>${price.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;
                        taxElement.innerHTML = `<h6>${tax.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</h6>`;
                        totalElement.innerHTML = `<h5><b>${total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h5>`;
                    }
                    
                   
                }
                amount.value = total;
                const isDirect = {!! json_encode($isDirect) !!};//blade escape
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = `{{ __('Continue Payment') }}`;
                    noDataBadge.style.display = 'none';
            }
        }else{
                // priceElement.innerHTML = ``;
                taxElement.innerHTML = ``;
                totalElement.innerHTML = ``;
                itemsElement.innerHTML = ``;
                confirmBtn.disabled = true;
                confirmBtn.textContent = `{{ __('License Not Available') }}`;
                noDataBadge.style.display = 'flex';
            }
    },
    error: function(xhr) {
        console.error(xhr.responseText);
    }
});
}

modalButton.addEventListener('click',function () {
    const selectedPackage = packageSelection.value;
    getModalData();
    getData(selectedPackage);
});

form.addEventListener('submit',function (e) {
    confirmBtn.disabled = true;
});
toggleSignageInput(packageSelection.value);

</script>


@endsection
