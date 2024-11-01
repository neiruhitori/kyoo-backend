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
            Menu Billing
        </h5>
    </div>

    @if ($unpaidInvoice)

    <div class="card text-white bg-info mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">Anda memiliki Invoice yang belum dibayar</h5>
            <div class="row">
                <div class="col mb-2">
                    <div>Nominal Harga yang dibayar:</div> 
                    <b>Rp. {{ number_format($unpaidInvoice->amount, 2, ',', '.') }}</b>
                </div>
                <div class="col mb-2">
                    <div>Kedaluarsa pada tanggal: </div>
                    <b>{{ \Carbon\Carbon::parse($unpaidInvoice->expiry_date)->translatedFormat('d F Y H:i')}}</b>
                </div>
                <div class="col mb-2">
                    <div>Status Pembayaran: </div>
                    <b><span class="badge badge-warning text-dark" >{{ $unpaidInvoice->status}}</span></b>
                </div>
                <div class="col mb-2">
                    <a href="{{ $unpaidInvoice->invoice_url}}" target="_blank" class="btn btn-primary py-2 px-3">Lakukan Pembayaran disini</a>
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
                            <h6 style="min-width: 150px;" class="pt-1">Pilihan Paket:</h6>
                            <div class="d-flex">
                                <select class="custom-select" id="packageSelection" name="packageSelection" style="max-width: 300px;"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                                    <option value="lite" {{ $subscription && $subscription->package == 'lite' ? 'selected' : '' }}>Lite</option>
                                    <option value="premium" {{ $subscription && $subscription->package == 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="custom" {{ $subscription && $subscription->package == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                        </div> 
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">Jenis Antrian:</h6>

                           @if ($isDirect)
                           <input style="max-width: 200px;" type="text" class="form-control" id="license_input" value="Onsite" readonly>
                           <input type="hidden" name="license_type" id="license_type" value="onsite">
                           @else
                           <input type="hidden" name="license_type" id="license_type" value="appointment">
                           <input style="max-width: 200px;" type="text" class="form-control" id="license_input" value="Appointment" readonly>
                           @endif
                        </div>
                        <div class="d-flex align-items-start mb-1">
                            <h6 style="min-width: 150px;" class="pt-2">Lama Langganan:</h6>
                            <select class="custom-select" style="max-width: 200px;" name="subs_duration" id="subs_duration"  {{ $unpaidInvoice ? 'disabled' : '' }}>
                                <option value="3" {{ $subscription && $subscription->subs_duration == '3' ? 'selected' : '' }}>3</option>
                                <option value="6" {{ $subscription && $subscription->subs_duration == '6' ? 'selected' : '' }}>6</option>
                                <option value="12" {{ $subscription && $subscription->subs_duration == '12' ? 'selected' : '' }}>12</option>
                              </select>
                              <p class="pt-2 ml-3">Bulan</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">Maksimum Antrian:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="queue" id="queue" min="100" max="500" value="{{ $subscription ? $subscription->queue  : '100' }}" readonly>
                            <p class="pt-2 ml-3">Antrian / Hari</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">Jumlah Meja:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="table" id="table"  min="1" max="5" value="{{ $subscription ? $subscription->max_table  : '1' }}" readonly>
                            <p class="pt-2 ml-3">Meja</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">Petugas Layanan:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="services" id="services" min="1" value="{{ $subscription ? $subscription->max_service  : '1' }}" readonly>
                            <p class="pt-2 ml-3">Petugas</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;" class="pt-1">Kiosk Antrian:</h6>
                            <input style="max-width: 200px;" type="number" class="form-control" name="kiosk" id="kiosk" min="1" value="{{ $subscription ? $subscription->kiosk  : '0' }}" readonly>
                            <p class="pt-2 ml-3">Perangkat</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div id="web-signage" style="display: flex">
                                <h6 style="min-width: 150px;" class="pt-1">Web Signage :</h6>
                                <input style="max-width: 200px;" type="number" class="form-control" name="signage" id="signage" min="1" value="1" readonly>
                                <p class="pt-2 ml-3">Perangkat</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-5">
                            <h6 style="min-width: 150px;" class="pt-1">Fitur:</h6>
                            <div id="feature">
                                <b></b>
                            </div>
                        </div>
                        <input type="hidden" name="amount" id="amount">
                       
                    </div>
                    <div class="d-flex align-items-center ml-4 mb-2">
                        @if ($unpaidInvoice)
                        <button class="btn btn-primary px-5" disabled>Selesaikan Pembayaran terlebih dahulu</button>
                        @else
                        <button class="btn btn-primary px-5" id="modalBtn" type="button" type="button" data-toggle="modal" data-target="#staticBackdrop" >Lanjutkan</button>
                        @endif
                    </div>
                    
                </div>


            </div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-dark" id="staticBackdropLabel"><b>Pembelian Lisensi Langganan</b></h5>
        </div>
        <div class="modal-body">
          <div class="row " style="color: #000">
            <div class="col-md-12">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>Pilihan Paket:</b></h6>
                        <div class="ml-2" style="min-width: 200px">
                            <h6 class="" id="md_license"></h6>
                        </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>Jenis Antrian:</b></h6>
                        <div class="ml-2" style="min-width: 200px">
                            <h6 class="" id="md_queue_type"></h6>
                        </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 105px"><b>Lama Langganan:</b></h6>
                        <div  class="ml-2" style="min-width: 200px">
                            <h6 class="" id="md_subsDuration"></h6>
                        </div>
                </div>
            </div>
            <div class="col-md-12" id="no_license_data">
                <h5><span class="badge badge-danger">Lisensi Tidak Tersedia</span></h5>
            </div>
        </div>

        <hr style="border-color:#000;">


        <div class="row" style="color: #000">
        <div class="col-md-6 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>Maks Antrian:</b></h6>
                    <div class="ml-2" style="min-width: 90px">
                        <h6 class="" id="md_queue"></h6>
                    </div>
            </div>
        </div>    

        <div class="col-md-6 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>Jumlah Meja:</b></h6>
                    <div class="ml-2" style="min-width: 90px">
                        <h6 class="" id="md_table"></h6>
                    </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>Maks. Petugas:</b></h6>
                    <div class="ml-2" style="min-width: 90px">
                        <h6 class="" id="md_service"></h6>
                    </div>
            </div>
        </div>

        <div class="col-md-6 d-flex">
            <div class="d-flex align-items-center mb-2">
                <h6 class="mr-2" style="min-width: 110px"><b>Kiosk Antrian:</b></h6>
                    <div class="ml-2" style="min-width: 90px">
                        <h6 class="" id="md_kiosk"></h6>
                    </div>
            </div>
        </div>

        <div class="col-md-6 " id="md_signage_container">
            <div class="d-flex align-items-center mb-2" >
                <h6 class="mr-2" style="min-width: 112px"><b>Web Signage:</b></h6>
                    <div id="signage" class="ml-2" style="min-width: 90px">
                        <h6 class="" id="md_signage"></h6>
                    </div>
            </div>
        </div>

          </div>

          <hr>

          <div class="row">
            <div class="col-md-12 d-flex">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Harga Lisensi :</b></h6>
                        <div id="price" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>

            <div class="col-md-12 d-flex">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Total Harga Item :</b></h6>
                        <div id="items" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>

            <div class="col-md-12 d-flex">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>PPN 11% :</b></h6>
                        <div id="tax" class="ml-2" style="min-width: 90px">
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
            
           
            <div class="col-md-12 d-flex">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mr-2" style="min-width: 112px"><b>Subtotal :</b></h6>
                        <div id="total" class="ml-2" style="min-width: 90px">
                            
                            <h6 class=""></h6>
                        </div>
                </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
          <button type="submit" id="confirmBtn" class="btn btn-primary">Konfirmasi</button>
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
    let subsDuration = document.getElementById('subs_duration'); 
    let packageSelection = document.getElementById('packageSelection'); 
    let form = document.getElementById('formInvoice');
    let queueType = document.getElementById('license_type');
    let modalButton = document.getElementById('modalBtn');
    let signage = document.getElementById('signage');
    let signageContainer = document.getElementById('web-signage');
    let modalSignage = document.getElementById('md_signage_container');
    let priceElement = document.getElementById('price');
    let taxElement = document.getElementById('tax');
    let itemsElement = document.getElementById('items');
    let totalElement = document.getElementById('total');
    let amount = document.getElementById('amount');

function toggleSignageInput(selectedPackage) {
    const isDirect = {!! json_encode($isDirect) !!};//blade escape
    updateFeatures(selectedPackage, isDirect);
    if (selectedPackage === 'premium') {
        queue.setAttribute('readonly', true);
        table.setAttribute('readonly', true);
        services.setAttribute('readonly', true);
        kiosk.setAttribute('readonly', true);

        queue.value = 500;
        table.value = 1;
        services.value = 3;
        kiosk.value = 0;
        signageContainer.style.display = 'flex';  
        modalSignage.style.display = 'flex';
        signage.setAttribute('readonly', true);   
        signage.value = 1; // Nilai default saat premium
    } else if (selectedPackage === 'custom') {
        queue.removeAttribute('readonly');
        table.removeAttribute('readonly');
        services.removeAttribute('readonly');
        kiosk.removeAttribute('readonly');
        table.addEventListener('input', function() {
        const tableValue = parseInt(table.value) || 0;
            services.value = tableValue * 3;
            });
        signageContainer.style.display = 'flex';
        modalSignage.style.display = 'flex';  
        signage.removeAttribute('readonly');  
        signage.value = 1;     
    } else {
        queue.setAttribute('readonly', true);
        table.setAttribute('readonly', true);
        services.setAttribute('readonly', true);
        kiosk.setAttribute('readonly', true);
        queue.value = 100;
        table.value = 1;
        services.value = 1;
        kiosk.value = 0;
        signageContainer.style.display = 'none';
        modalSignage.style.display = 'none';  
        signage.value = '';                      
    }
    updateFeatures(selectedPackage, isDirect);
                      
}

function updateFeatures(selectedPackage, isDirect) {
    let featuresText = '';

    if (selectedPackage === 'lite') {
        featuresText = isDirect 
            ? 'Linktree dari Webtokoo' 
            : 'Email notifikasi dan Linktree dari Webtokoo';
    } else if (selectedPackage === 'premium') {
        featuresText = isDirect 
            ? 'Panggilan Suara, Monitoring TV Antrian, Antrian hybrid Appointment, Linktree dari Webtokoo'
            : 'Email Notifikasi, WA Notifikasi, Linktree dari Webtokoo';
    } else if (selectedPackage === 'custom') {
        featuresText = isDirect 
            ? 'Panggilan Suara, Monitoring TV Antrian, Antrian hybrid Appointment, Web Survey dan Linktree dari Webtokoo'
            : 'Email Notifikasi, WA Notifikasi, Linktree dari Webtokoo';
    }

    feature.innerHTML = `<b>${featuresText}</b>`;
}


function getModalData() {
    let tableVal = table.value;
    let queueVal = queue.value;
    let serviceVal = services.value;
    let kioskVal = kiosk.value;
    let packageVal = '';
    let subsDurationVal = subsDuration.value;
    let queueTypeVal = '';


    if(packageSelection.value === "custom"){
        let signageVal = signage.value;
        document.getElementById('md_signage').innerHTML = signageVal + " Perangkat";
        packageVal = "Lisensi Langganan Custom";
        
    }else if(packageSelection.value === "premium"){
        let signageVal = signage.value;
        document.getElementById('md_signage').innerHTML = signageVal + " Perangkat";
        packageVal = "Lisensi Langganan Premium";
    }else{
        packageVal = "Lisensi Langganan Lite";
    }
    //tipe antrian
    if(queueType.value == "onsite"){
        queueTypeVal = "Antrian Onsite";
    }else{
        queueTypeVal = "Antrian Appointment";
    }

    document.getElementById('md_license').innerHTML = packageVal;
    document.getElementById('md_queue_type').innerHTML = queueTypeVal;
    document.getElementById('md_subsDuration').innerHTML = subsDurationVal + " Bulan";
    document.getElementById('md_queue').innerHTML = queueVal + " Antrian";
    document.getElementById('md_table').innerHTML = tableVal + " Meja";
    document.getElementById('md_service').innerHTML = serviceVal + " Petugas";
    document.getElementById('md_kiosk').innerHTML = kioskVal + " Perangkat";
}

subsDuration.addEventListener('change', function() {
    const selectedPackage = packageSelection.value;
});

packageSelection.addEventListener('change', function() {
    const selectedPackage = packageSelection.value;
    toggleSignageInput(selectedPackage);
});


function calculateTotal(price, itemPrices) {
    const tax = price * 0.11; // PPN 11%
    const total = price + tax + itemPrices;

    return { price, tax, total};
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
            console.log(data);
            
            if (data) {  
                    const { price, tax, total, itemPrices } = calculateTotal(data.license_prices, data.item_prices);

                    priceElement.innerHTML = `<h6><b>${price.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h6>`;
                    taxElement.innerHTML = `<h6><b>${tax.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h6>`;
                    totalElement.innerHTML = `<h6><b>${total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h6>`;
                    itemsElement.innerHTML = `<h6><b>${data.item_prices.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</b></h6>`;
                    amount.value = total;
                     const isDirect = {!! json_encode($isDirect) !!};//blade escape
                     confirmBtn.disabled = false;
                     confirmBtn.textContent = 'Konfirmasi';
                     noDataBadge.style.display = 'none';
            }
        }else{
                priceElement.innerHTML = ``;
                taxElement.innerHTML = ``;
                totalElement.innerHTML = ``;
                itemsElement.innerHTML = ``;
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Lisensi Tidak Tersedia';
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
