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



    <div class="mb-5 mx-3">
        @if ($unpaidInvoice)
            <h5 class="font-weight-bold mb-0" style="color: #103C7C;">
                {{ __('Subscription') }} - Current Plan Summary
            </h5>
        @else
            <h5 class="font-weight-bold mb-0" style="color: #103C7C;">
            {{ __('Subscription') }}
            </h5>
        @endif
    </div>

    @if ($unpaidInvoice && $subscription)
    <div class="card shadow-sm rounded mb-4">
        <div class="text-white rounded" style="background-color: #BF1D08">
            <div class="d-flex justify-content-center py-2 align-items-center">
                <i class="fas fa-clock mr-2"></i> 
                Payment Pending
            </div>
            <div class="card-body rounded" style="border-top-right-radius: 1rem; border-top-left-radius: 1rem; background-color: #fff; color: black; border:1px solid #fff">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-3">{{ __('Invoice Notification') }}</h5>
                    <h6 class="mr-3">{{ __('Expires On') }}: <span class="text-danger">{{ \Carbon\Carbon::parse($unpaidInvoice->expiry_date)->translatedFormat('d F Y H:i')}}</span></h6>
                </div>
                <div class="row p-3 rounded" style="background-color: #FDD5CE4D;">
                    <div class="col mb-2">
                        <h4>{{ Str::ucfirst($subscription->package) }}</h4>
                        <div style="color: #637381">License Type</div>
                    </div>
                    <div class="col mb-2">
                        <h4>{{ $unpaidInvoice->currency ?? 'Rp' }}. {{ number_format($unpaidInvoice->amount, 2, ',', '.') }}</h4>
                        <div style="color: #637381">{{ __('Paid Amount') }}</div>
                    </div>
                    <div class="col mb-2 text-right align-content-center">
                        <a href="{{ $unpaidInvoice->invoice_url}}" target="_blank" class="btn btn-danger py-2 px-5" id="payButton">{{ __('Pay Here') }}</a>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="card shadow">
        <div class="card-body">
            <div class="row p-3">
                <div class="col-md-3 mb-5">
                    <h5>License Type</h5>
                    <h5 style="color: #000">{{ Str::ucfirst($subscription->package) }}</h5>
                </div>
                <div class="col-md-3 mb-5">
                    <h5>Queue Type</h5>
                    <h5 style="color: #000">{{ Str::ucfirst($subscription->license_type) }}</h5>
                </div>
                <div class="col-md-3 mb-5">
                    <h5>Subs. Duration</h5>
                    <h5 style="color: #000">{{ $subscription->subs_duration }} {{ __('Month') }}</h5>
                </div>
                <div class="col-md-3 mb-5">
                    <h5>Maximum Queue</h5>
                    <h5 style="color: #000">{{ $subscription->queue }}/{{ __('Day') }}</h5>
                </div>


                <div class="col-md-3">
                    <h5>Counter Amount</h5>
                    <h5 style="color: #000">{{ $subscription->max_table }} {{ __('Workstation') }}</h5>
                </div>
                <div class="col-md-3">
                    <h5>Staff</h5>
                    <h5 style="color: #000">{{ $subscription->max_service }}</h5>
                </div>
                <div class="col-md-3">
                    <h5>Web Kiosk</h5>
                    <h5 style="color: #000">{{ $subscription->kiosk }} {{ __('Device') }}</h5>
                </div>
                {{-- <div class="col-md-3">
                    <h5>Web Signage TV</h5>
                    <h5 style="color: #000">1 Device</h5>
                </div> --}}
            </div>
        </div>
      </div>

    @else
      

    <div class="card shadow mb-4">
        <div class="card-body">     
            <form action="" id="formInvoice" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="packageSelection" style="color: #000">{{ __('License Type') }}</label>
                        <select class="form-control" id="packageSelection" name="packageSelection"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                            <option value="lite" {{ $subscription && $subscription->package == 'lite' ? 'selected' : '' }}>Lite</option>
                            <option value="premium" {{ $subscription && $subscription->package == 'premium' ? 'selected' : '' }}>Premium</option>

                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="license_type" style="color: #000">{{ __('Queue Type') }}</label>
                        @if ($isDirect)
                            <input type="text" class="form-control" id="license_input" value="Onsite" readonly>
                            <input type="hidden" name="license_type" id="license_type" value="onsite">
                       @else
                            <input type="hidden" name="license_type" id="license_type" value="appointment">
                            <input type="text" class="form-control" id="license_input" value="Appointment" readonly>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="subs_duration" style="color: #000">{{ __('Subs. Duration') }}</label>
                        <select class="custom-select" name="subs_duration" id="subs_duration"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                            <option value="3" {{ $subscription && $subscription->subs_duration == '3' ? 'selected' : '' }}>3 {{ __('Month') }}</option>
                            <option value="6" {{ $subscription && $subscription->subs_duration == '6' ? 'selected' : '' }}>6 {{ __('Month') }}</option>
                            <option value="12" {{ $subscription && $subscription->subs_duration == '12' ? 'selected' : '' }}>12 {{ __('Month') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="queue" style="color: #000">{{ __('Maximum Queue') }}/{{ __('Day') }}</label>
                        <input type="number" class="form-control" name="queue" id="queue" min="100" max="500" required value="{{ $subscription ? $subscription->queue  : '100' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="table" style="color: #000">{{ __('Counter Amount') }}</label>
                        <input type="number" class="form-control" name="table" id="table"  min="1" max="5" required value="{{ $subscription ? $subscription->max_table  : '1' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                         <label for="services" style="color: #000">{{ __('Staff') }}</label>
                         <input type="number" class="form-control" name="services" id="services" min="1" max="15" required value="{{ $subscription ? $subscription->max_service  : '1' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3" id="web-kiosk">
                        <label for="services" style="color: #000">{{ __('Webkiosk') }}</label>
                        <input type="number" class="form-control" name="kiosk" id="kiosk" min="1" max="3" value="1" required readonly>
                    </div>
                    <div class="col-md-6 mb-3" id="web-signage">
                        <label for="services" style="color: #000">{{ __('Web Signage TV') }}</label>
                        <input type="number" class="form-control" name="signage" id="signage" min="1" max="3" value="1" required readonly>
                    </div>
                    <input type="hidden" name="amount" id="amount">
    
                    <div class="col-md-12 text-right">
                        @if ($unpaidInvoice)
                            <button class="btn btn-primary px-5" disabled>{{ __('Complete Payment First') }}</button>
                        @else
                            <button class="btn btn-primary px-5" id="modalBtn" 
                            type="button" type="button" data-toggle="modal" 
                            data-target="#staticBackdrop" style="background-color: #103C7C; color: #fff;">
                                    {{ __('Continue') }}
                            </button>
                        @endif
                    </div>
                </div>
            {{-- </form> --}}
        </div>
    </div>

@endif
    


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        {{-- <div class="modal-header">
        </div> --}}
        <div class="modal-body">
        <h5 class="modal-title mb-4" style="color: #103C7C;" id="staticBackdropLabel"><b>{{ __('KYOO Subscription') }}</b></h5>

        <div class="card rounded mb-4">
            <div class="text-white rounded-top" style="background-color: #103C7C">
                <div class="d-flex justify-content-center py-2 align-items-center">
                    License Feature
                </div>
            </div>
            <div class="card-body rounded">
                 <div class="row p-3">
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('License Type') }}</h5>
                        <h5 style="color: #000" id="md_license"></h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Queue Type') }}</h5>
                        <h5 style="color: #000" id="md_queue_type"></h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Subs. Duration') }}</h5>
                        <h5 style="color: #000" id="md_subsDuration"></h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Maximum Queue') }}</h5>
                        <h5 style="color: #000" id="md_queue"></h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Counter Amount') }}</h5>
                        <h5 style="color: #000" id="md_table"></h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>{{ __('Staff') }}</h5>
                        <h5 style="color: #000" id="md_service"></h5>
                    </div>
                    <div class="col-md-12" id="no_license_data">
                        <div class="p-3 rounded text-center" style="background-color: #fdd5ce">
                            <h5 style="color: #BF1D08"><i class="fas fa-exclamation-circle mr-3"></i>{{ __('License Not Available') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="summaryContainer">
            <table class="table table-bordered rounded" style="color: #000" id="itemContainer">
                    <thead class="text-center" style="background-color:#33A0FF4D; color: #103C7C;">
                      <tr>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Period') }}</th>
                        <th>{{ __('Item Price') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <tr>
                        <td>{{ __('Workstation') }}</td>
                        <td id="tableQty"></td>
                        <td class="itemPeriod"></td>
                        <td id="tablePrice"></td>
                        <td id="tableTotalPrice"></td>
                      </tr>
                      <tr id="tableSignage">
                        <td>Web Signage</td>
                        <td id="signageQty"></td>
                        <td class="itemPeriod"></td>
                        <td id="signagePrice"></td>
                        <td id="signageTotalPrice"></td>
                      </tr>
                      <tr id="tableKiosk">
                        <td>Web Kiosk</td>
                        <td id="kioskQty"></td>
                        <td class="itemPeriod"></td>
                        <td id="kioskPrice"></td>
                        <td id="kioskTotalPrice"></td>
                      </tr>
                      {{-- <tr>
                        <th colspan="4" class="text-right ">Subtotal Item :</th>
                        <td id="items"></td>
                      </tr> --}}
                    </tbody>
            </table>

            <div class="row justify-content-end mb-3" id="subtotal">
                  <div class="col-md-3 text-right">
                      <h6>
                          <b style="color: #000">
                              {{ __('SUBTOTAL') }}
                          </b>
                      </h6>
                  </div>
                  <div class="col-md-3 text-center">
                      <b class="text-danger" id="subtotalContent"><h6></h6></b>
                  </div>
            </div>


            <div class="row justify-content-end" id="formTax">
                  <div class="col-md-3 text-right">
                      <h6>
                          <b style="color: #000">
                              {{ __('VAT 11%') }}
                          </b>
                      </h6>
                  </div>
                  <div class="col-md-3 text-center">
                      <b class="text-danger" id="taxContent"><h6></h6></b>
                  </div>
            </div>


            <div class="row justify-content-end mb-3">
                  <div class="col-md-3 text-right">
                      <h6>
                          <b style="color: #000">
                              {{ __('TOTAL') }}
                          </b>
                      </h6>
                  </div>
                  <div class="col-md-3 text-center">
                      <b class="text-danger" id="totalContent"><h6></h6></b>
                  </div>
            </div>

        </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{{ __('Cancel') }} </button>
          <button type="submit" id="confirmBtn" class="btn btn-primary" style="background-color: #103C7C">{{ __('Continue Payment') }} </button>
        </div>
      </div>
      </form>
    </div>
</div>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
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
    let priceElement = document.getElementById('price');
    let taxElement = document.getElementById('tax');
    let itemsElement = document.getElementById('items');
    let totalElement = document.getElementById('total');
    let amount = document.getElementById('amount');
    let payButton = document.getElementById("payButton");
    let unpaidStatus = document.getElementById("unpaidStatus");

    limitInput(queue,100,500,packageSelection);
    limitInput(table,1,5,packageSelection);
    limitInput(services,1,15,packageSelection);

    function limitInput(element, min, max, packageSelection = null) {
        element.addEventListener('input', function () {
            let value = parseInt(this.value) || min;
            if(element.id == 'queue'){
                if (packageSelection) {
                    max = (packageSelection.value === 'lite') ? 100 : 500; 
                }
            }
            this.value = Math.min(Math.max(value, min), max);
        });
    }

function toggleInput(selectedPackage) {
    const isDirect = {!! json_encode($isDirect) !!}; // Blade escape
    const country = "{{ Auth::user()->Branch->country }}";

    queue.removeAttribute('readonly');
    table.removeAttribute('readonly');
    services.removeAttribute('readonly');

    signageContainer.style.display = 'block';
        
        if (selectedPackage === 'premium') {
            queue.removeAttribute('readonly');
            setValues({
                    queue: 500,
                    table: 1,
                    services: 2,
                    kiosk: 1,
                    signage: 1
                });
            signageContainer.style.display = 'block';
            kioskContainer.style.display = 'block';
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
                    signage: 0
                });
            signageContainer.style.display = 'none';
            kioskContainer.style.display = 'none';
        }
        return;
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

function getModalData() {
    let tableVal = table.value;
    let queueVal = queue.value;
    let serviceVal = services.value;
    let packageVal = '';
    let subsDurationVal = subsDuration.value;
    let queueTypeVal = queueType.value == "onsite" ? 'Onsite' : 'Appointment';

    if(packageSelection.value === "premium"){
        let signageVal = signage.value;
        let kioskVal = kiosk.value;        
        packageVal = "Premium";
    }else{
        packageVal = "Lite";
    }
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
    toggleInput(selectedPackage);
});


function calculateTotal(price, country = 'Indonesia') {
    let tax = 0;
    if (country == 'Indonesia') {
        tax = price * 0.11;
    }
    const total = price + tax;
    return {price, tax, total}
}

function setCurrency(price, country = 'Indonesia'){
    const locale = country == "Indonesia" ? 'id-ID' : 'en-US';
    const currency =  country == "Indonesia" ? 'IDR' : 'USD';

    return price.toLocaleString(locale, { style: 'currency', currency: currency })
}

function getData(selectedPackage){
    const selectedDuration = parseInt(subsDuration.value);
    let summaryCtn = document.getElementById('summaryContainer');
    let noDataBadge = document.getElementById('no_license_data');
    let itemCtn = document.getElementById('itemContainer');
    let tableQty = document.getElementById('tableQty');
    let tablePrice = document.getElementById('tablePrice');
    let tableTotalPrice = document.getElementById('tableTotalPrice');
    let tableSignage = document.getElementById('tableSignage');
    let signageQty = document.getElementById('signageQty');
    let kioskQty = document.getElementById('kioskQty');
    let signagePrice = document.getElementById('signagePrice');
    let kioskPrice = document.getElementById('kioskPrice');
    let signageTotalPrice = document.getElementById('signageTotalPrice');
    let kioskTotalPrice = document.getElementById('kioskTotalPrice');
    let tableKiosk = document.getElementById('tableKiosk');
    let formTax = document.getElementById('formTax');
    let subtotal = document.getElementById('subtotal');
    let taxContent = document.getElementById('taxContent');
    let totalContent = document.getElementById('totalContent');
    let subtotalContent = document.getElementById('subtotalContent');
    let itemPeriod = document.querySelectorAll('.itemPeriod');
    
    let license = selectedPackage;
    let tableVal = table.value;
    let kioskVal = kiosk.value;
    let signageVal = signage.value;

    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Loading...';
    summaryCtn.style.display = 'none';
    noDataBadge.style.display = 'none';
    formTax.style.display = "none"
    subtotal.style.display = "none"
    itemCtn.style.display = 'none';
    tableSignage.style.display = 'none';
    tableKiosk.style.display = 'none';

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
                itemPeriod.forEach(el => {
                    el.textContent = `${selectedDuration} {{ __('Month') }}`;
                });
                if(signageVal && signageVal != 0){
                    tableSignage.style.display = 'revert';
                    const signagePricePerMonth = data.total_signage_prices / signageVal / selectedDuration;
                    signageQty.textContent = signageVal;
                    signagePrice.textContent = `${setCurrency(signagePricePerMonth, data.country)} / {{ __('Month') }}`;
                    signageTotalPrice.textContent = `${setCurrency(data.total_signage_prices, data.country)}`;
                }
                if(kioskVal && kioskVal != 0){
                    tableKiosk.style.display = 'revert';
                    const kioskPricePerMonth = data.total_kiosk_prices / kioskVal / selectedDuration;
                    kioskQty.textContent = kioskVal;
                    kioskPrice.textContent = `${setCurrency(kioskPricePerMonth, data.country)} / {{ __('Month') }}`;
                    kioskTotalPrice.textContent = `${setCurrency(data.total_kiosk_prices, data.country)}`;
                }
                const { price, tax, total, itemPrices } = calculateTotal(data.final_total, data.country);
                const finalPrice = total;
                console.log({ price, tax, total, itemPrices })
                if(data.country == 'Indonesia'){
                    taxContent.textContent = `${setCurrency(tax,data.country)}`;
                    formTax.style.display = "flex"
                }
                if(selectedPackage == 'premium'){
                    subtotal.style.display = "flex"
                    subtotalContent.textContent = `${setCurrency(data.final_total,data.country)}`
                }
                tableQty.textContent = tableVal;
                const tablePricePerMonth = data.total_table_prices / selectedDuration / tableVal;
                tablePrice.textContent = `${setCurrency(tablePricePerMonth, data.country)} / {{ __('Month') }}`;
                tableTotalPrice.textContent = `${setCurrency(data.total_table_prices,data.country)}`;
                totalContent.textContent = `${setCurrency(finalPrice,data.country)}`
                amount.value = finalPrice;
                const isDirect = {!! json_encode($isDirect) !!};//blade escape
                confirmBtn.disabled = false;
                confirmBtn.textContent = `{{ __('Continue Payment') }}`;
                summaryCtn.style.display = 'revert';
                itemCtn.style.display = 'revert';
                noDataBadge.style.display = 'none';
            }
        }else{
                confirmBtn.disabled = true;
                confirmBtn.textContent = `{{ __('License Not Available') }}`;
                noDataBadge.style.display = 'revert';
                summaryCtn.style.display = 'none';
                itemCtn.style.display = 'none';
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
    toggleInput(packageSelection.value);

</script>


@endsection
