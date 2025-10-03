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
            {{ __('Subscription') }} - Current Plan Summary
            </h5>
        @endif
    </div>
        @if ($unpaidInvoice)
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
                                <h4>{{ Str::ucfirst($unpaidSubs->package) }}</h4>
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
                            <h5 style="color: #000">{{ Str::ucfirst($unpaidSubs->package) }}</h5>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Queue Type</h5>
                            <h5 style="color: #000">{{ Str::ucfirst($unpaidSubs->license_type) }}</h5>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Subs. Duration</h5>
                            <h5 style="color: #000">{{ $unpaidSubs->subs_duration }} {{ __('Month') }}</h5>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Maximum Queue</h5>
                            <h5 style="color: #000">{{ $unpaidSubs->queue }}/{{ __('Day') }}</h5>
                        </div>


                        <div class="col-md-3">
                            <h5>Counter Amount</h5>
                            <h5 style="color: #000">{{ $unpaidSubs->max_table }} {{ __('Workstation') }}</h5>
                        </div>
                        <div class="col-md-3">
                            <h5>Staff</h5>
                            <h5 style="color: #000">{{ $unpaidSubs->max_service }}</h5>
                        </div>
                        <div class="col-md-3">
                            <h5>Web Kiosk</h5>
                            <h5 style="color: #000">{{ $unpaidSubs->kiosk }} {{ __('Device') }}</h5>
                        </div>
                        {{-- <div class="col-md-3">
                            <h5>Web Signage TV</h5>
                            <h5 style="color: #000">1 Device</h5>
                        </div> --}}
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow-sm rounded mb-4">
                <div class="text-white rounded" style="background-color: #103c7c">
                    <div class="d-flex justify-content-center py-2 align-items-center">
                        <i class="fas fa-clock mr-2"></i> 
                        Extend Subscription
                    </div>
                    <div class="card-body rounded" style="border-top-right-radius: 1rem; border-top-left-radius: 1rem; background-color: #fff; color: black; border:1px solid #fff">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-3">License anda saat ini :</h5>
                            
                        </div>
                        <div class="row p-3 rounded" style="background-color: #33A0FF4D;">
                            <div class="col mb-2">
                                <h4>{{ Str::ucfirst($currentSubs->package) }}</h4>
                                <div style="color: #637381">License Type</div>
                            </div>
                            <div class="col mb-2">
                                <h4>{{ $invoice->currency ?? 'Rp' }}. {{ number_format($invoice->amount, 2, ',', '.') }}</h4>
                                <div style="color: #637381">{{ __('Paid Amount') }}</div>
                            </div>
                            <div class="col mb-2 text-right align-content-center">
                                <button class="btn py-2 px-5" style="background-color: #103C7C; color:white" id="extendBtn" data-toggle="modal" data-target="#staticBackdrop">Perpanjang License</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="card shadow">
            <form action="{{ route('admin-branch.subscription.store') }}" method="POST">
            @csrf
                <div class="card-body">
                    <div class="row p-3">
                        <div class="col-md-3 mb-5">
                            <h5>License Type</h5>
                            <h5 style="color: #000">{{ Str::ucfirst($currentSubs->package) }}</h5>
                            <input type="hidden" name="packageSelection" value="{{ $currentSubs->package }}" readonly>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Queue Type</h5>
                            <h5 style="color: #000">{{ Str::ucfirst($currentSubs->license_type) }}</h5>
                            <input type="hidden" name="license_type" value="{{ $currentSubs->license_type }}" readonly>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Subs. Duration</h5>
                            <h5 style="color: #000">{{ $currentSubs->subs_duration }} {{ __('Month') }}</h5>
                            <input type="hidden" name="subs_duration" value="{{ $currentSubs->subs_duration }}" readonly>
                        </div>
                        <div class="col-md-3 mb-5">
                            <h5>Maximum Queue</h5>
                            <h5 style="color: #000">{{ $currentSubs->queue }}/{{ __('Day') }}</h5>
                            <input type="hidden" name="queue" value="{{ $currentSubs->queue }}" readonly>
                        </div>
                        
                        
                        <div class="col-md-3">
                            <h5>Counter Amount</h5>
                            <h5 style="color: #000">{{ $currentSubs->max_table }} {{ __('Workstation') }}</h5>
                            <input type="hidden" name="table" value="{{ $currentSubs->max_table }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <h5>Staff</h5>
                            <h5 style="color: #000">{{ $currentSubs->max_service }}</h5>
                            <input type="hidden" name="services" value="{{ $currentSubs->max_service }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <h5>Web Kiosk</h5>
                            <h5 style="color: #000">{{ $currentSubs->kiosk }} {{ __('Device') }}</h5>
                            <input type="hidden" name="kiosk" value="{{ $currentSubs->kiosk }}" readonly>
                            <input type="hidden" name="signage" value="{{ $currentSubs->kiosk }}" readonly>
                            <input type="hidden" name="amount" value="{{ $invoice->amount }}" readonly>
                        </div>
                        {{-- <div class="col-md-3">
                            <h5>Web Signage TV</h5>
                            <h5 style="color: #000">1 Device</h5>
                        </div> --}}
                    </div>
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
                                <h5 style="color: #000">{{ Str::ucfirst($currentSubs->package) }}</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>{{ __('Queue Type') }}</h5>
                                <h5 style="color: #000">{{ Str::ucfirst($currentSubs->license_type) }}</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>{{ __('Subs. Duration') }}</h5>
                                <h5 style="color: #000" >{{ $currentSubs->subs_duration }} {{ __('Month') }}</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>{{ __('Maximum Queue') }}</h5>
                                <h5 style="color: #000">{{ $currentSubs->queue }}/{{ __('Day') }}</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>{{ __('Counter Amount') }}</h5>
                                <h5 style="color: #000">{{ $currentSubs->max_table }} {{ __('Workstation') }}</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>{{ __('Staff') }}</h5>
                                <h5 style="color: #000">{{ $currentSubs->max_service }}</h5>
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
                                <td id="tableQty">{{ $data['tableQty'] }}</td>
                                <td class="itemPeriod">{{ $data['duration'] }}</td>
                                <td id="tablePrice">
                                    {{ $data['tablePrice'] }}/ {{ __('Month') }}
                                </td>
                                <td id="tableTotalPrice">{{ $data['tableTotal'] }}</td>
                            </tr>
                            @if ($data['signageQty'] !=0)
                                <tr id="tableSignage">
                                    <td>Web Signage</td>
                                    <td id="signageQty">{{ $data['signageQty'] }}</td>
                                    <td class="itemPeriod">{{ $data['duration'] }}</td>
                                    <td id="signagePrice"> {{ $data['signagePrice'] }}/ {{ __('Month') }}</td>
                                    <td id="signageTotalPrice">{{ $data['signageTotal'] }}</td>
                                </tr>
                                <tr id="tableKiosk">
                                    <td>Web Kiosk</td>
                                    <td id="kioskQty">{{ $data['kioskQty'] }}</td>
                                    <td class="itemPeriod">{{ $data['duration'] }}</td>
                                    <td id="kioskPrice">{{ $data['kioskPrice'] }}/ {{ __('Month') }}</td>
                                    <td id="kioskTotalPrice">{{ $data['kioskTotal'] }}</td>
                                </tr>
                            @endif
                            </tbody>
                    </table>

                    <div class="row justify-content-end" id="subtotal">
                        <div class="col-md-3 text-right">
                            <h6>
                                <b style="color: #000">
                                    {{ __('SUBTOTAL') }}
                                </b>
                            </h6>
                        </div>
                        <div class="col-md-3 text-center">
                            <b class="text-danger" id="subtotalContent"><h6>{{ $data['subtotal'] }}</h6></b>
                        </div>
                    </div>

                    @if ($data['currency'] == 'IDR')
                        <div class="row justify-content-end" id="formTax">
                            <div class="col-md-3 text-right">
                                <h6>
                                    <b style="color: #000">
                                        {{ __('VAT 11%') }}
                                    </b>
                                </h6>
                            </div>
                            <div class="col-md-3 text-center">
                                <b class="text-danger" id="taxContent"><h6>{{ $data['tax'] }}</h6></b>
                            </div>
                        </div>
                    @endif


                    <div class="row justify-content-end mt-3 mb-3">
                        <div class="col-md-3 text-right">
                            <h6>
                                <b style="color: #000">
                                    {{ __('TOTAL') }}
                                </b>
                            </h6>
                        </div>
                        <div class="col-md-3 text-center">
                            <b class="text-danger" id="totalContent"><h6>{{ $data['grandTotal'] }}</h6></b>
                        </div>
                    </div>

                </div>

                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{{ __('Cancel') }} </button>
                @if ($isAlmostExpired)
                    <button type="submit" id="confirmBtn" class="btn btn-primary" style="background-color: #103C7C">{{ __('Continue Payment') }} </button>
                @else
                    <button type="button" class="btn btn-primary" disabled style="background-color: #103C7C">{{ __('Tidak bisa melakukan aksi') }} </button>
                @endif
                </div>
            </div>
            </form>
            </div>
        </div>





<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@endsection
