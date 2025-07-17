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

        .round-checkbox input[type="checkbox"] {
            opacity: 0;
            position: absolute;
        }


        .round-checkbox label {
            display: inline-flex;
            align-items: center;
            position: relative;
            padding-left: 2rem;
            user-select: none;
        }


        .round-checkbox label::before {
            content: "";
            position: absolute;
            left: 0;
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid #999;
            border-radius: 50%;
            background-color: white;
            transition: all 0.2s ease;
        }


        .round-checkbox input[type="checkbox"]:checked + label::before {
            background-color: #103C7C;
            border-color: #103C7C;
        }

        .round-checkbox label::after {
            content: "";
            position: absolute;
            left: 0.4rem;
            top: 0.35rem;
            width: 0.35rem;
            height: 0.65rem;
            border: solid white;
            border-width: 0 2px 2px 0;
            opacity: 0;
            transform: rotate(45deg);
            transition: opacity 0.2s ease;
        }

        .round-checkbox input[type="checkbox"]:checked + label::after {
            opacity: 1;
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

<div class="card shadow mb-4">
    <div class="card-header pb-0">
        <h5 class="font-weight-bold" style="color: #103C7C">License</h5>
    </div>
    <div class="card-body">
        <h4 class="mb-5" style="color: #000">License Detail</h4>
            <div class="d-flex align-items-center mb-3">
                <h6 style="min-width: 150px;">{{ __('License Type') }}:</h6>
                <h6 style="color: #000"><b>{{ Auth::user()->Branch->BranchType->name ?? "Trial License" }}</b></h6>
                @if (!Auth::user()->Branch->BranchType->is_premium)
                    <h3 class="mx-3">|</h3>
                    <a href="{{ route('admin-branch.subscription') }}" 
                        class="btn btn-primary" 
                        style="background-color: #103C7C">{{ __('KYOO Subscription') }}</a>
                @endif
            </div>
            <div class="d-flex align-items-center mb-3">
                <h6 style="min-width: 150px;">{{ __('Active Period') }}:</h6>
                <h6 style="color: #000"> 
                    <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->updated_at)->translatedFormat('d F Y') }}</b> - 
                    <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->license_expiration_date)->translatedFormat('d F Y') }}</b>
                </h6>
            </div> 
    </div>
    <div class="card-body border-top">
        <div class="mb-3 mt-2">
                <h5 class="mb-3">{{ __('Feature') }}</h5>
                <div class="row">
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="round-checkbox">
                                    <input type="checkbox" checked disabled>
                                    <label for="myCheck">{{ Auth::user()->Branch->max_counter }} {{ __('Workstation') }}</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                    <div class="round-checkbox">
                                        <input type="checkbox" checked disabled>
                                        <label for="myCheck">{{ Auth::user()->Branch->BranchConfiguration->max_services}} {{ __('Staff') }}</label>
                                    </div>
                            </div>
                            <div class="col-sm-6">
                                    <div class="round-checkbox">
                                        <input type="checkbox" checked disabled>
                                        <label for="myCheck">{{ Auth::user()->Branch->max_queue }} {{ __('Queue') }}</label>
                                    </div>
                            </div>
                            @foreach ($features as $val)
                                <div class="col-sm-6">
                                    <div class="round-checkbox">
                                        <input type="checkbox" checked disabled>
                                        <label for="myCheck">{{ __($val->additionalFeature->name) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header pb-0">
        <h5 class="font-weight-bold" style="color: #103C7C">Invoice</h5>
    </div>
        <div class="card-body">
        <h4 style="color: #000">Invoice History</h4>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-4" id="table">
            <thead style="background-color:#33A0FF4D; color: #103C7C;">
                <tr class="text-center">
                    <th scope="col">{{ __('Date') }}</th>
                    <th scope="col">{{ __('Description') }}</th>
                    <th scope="col">{{ __('Nominal Amount') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Action') }}</th>
                </tr>
            </thead>
                <tbody>
                    @if ($invoices->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No Invoice') }}.</td>
                        </tr>    
                    @else
                    @foreach ($invoices as $inv)
                        <tr>
                            <th class="text-center">{{ \Carbon\Carbon::parse($inv->created_at)->translatedFormat('d-m-Y') }}</th>
                            <td class="invoice-description" style="width: 40%;">
                                {{ app()->getLocale() == 'en' ? ($inv->description_en ?? $inv->description) : $inv->description }}
                            </td>
                            <td class="text-center">
                                @if($inv->currency == 'USD')
                                    {{ $inv->currency ?? 'USD' }} ${{ number_format($inv->amount, 2) }}
                                @else
                                    Rp {{ number_format($inv->amount, 2, ',', '.') }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($inv->status == "PAID")
                                    <span class="badge badge-pill px-2 py-1"
                                    style="background-color:#C1F0DF; color: #1CC88A;">
                                        {{$inv->status}}
                                    </span>
                                @elseif($inv->status == "PENDING")
                                    <span class="badge badge-pill px-2 py-1"
                                    style="background-color:#ffedbc; color: #fcac16;">
                                        {{$inv->status}}
                                    </span>
                                @else
                                    <span class="badge badge-pill px-2 py-1" 
                                    style="background-color:#FDD5CE; color: #BF1D08;">
                                        {{$inv->status}}
                                    </span>
                                @endif 
                            </td>
                            <td class="text-center">
                                @if ($inv->status == "PENDING")
                                    <a href="{{ $inv->invoice_url }}" class="btn btn-danger px-4">
                                        <i class="fas fa-credit-card"></i>
                                        Bayar
                                    </a>
                                @endif
                                <button onclick="printInvoice('{{ $inv->id_invoice }}')" class="btn btn-warning px-3" title="{{ __('Download Invoice History') }}">
                                    <i class="fas fa-download"></i>
                                    Download
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
        </table>
    </div>
</div>

            {{-- <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade mx-2 my-4" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                    <div class="mx-4 my-4">
                        <h4 class="mb-3">Invoice History</h4>
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('Description') }}</th>
                                <th scope="col">{{ __('Nominal Amount') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                              </tr>
                            </thead>
                            <tbody>
                                @if ($invoices->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No Invoice') }}.</td>
                                </tr>    
                                @else
                                @foreach ($invoices as $inv)
                                <tr>
                                    <th>{{ \Carbon\Carbon::parse($inv->created_at)->translatedFormat('d F Y') }}</th>
                                    <td class="invoice-description">
                                        {{ app()->getLocale() == 'en' ? ($inv->description_en ?? $inv->description) : $inv->description }}
                                    </td>
                                    <td>
                                        @if($inv->currency == 'USD')
                                            {{ $inv->currency ?? 'USD' }} ${{ number_format($inv->amount, 2) }}
                                        @else
                                            Rp {{ number_format($inv->amount, 2, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($inv->status == "PAID")
                                        <b><span class="badge badge-success">{{$inv->status}}</span></b>
                                        @elseif($inv->status == "PENDING")
                                        <b><span class="badge badge-warning text-dark">{{$inv->status}}</span></b>
                                        @else
                                        <b><span class="badge badge-danger">{{$inv->status}}</span></b>
                                        @endif 
                                    </td>
                                    <td><button onclick="printInvoice('{{ $inv->id_invoice }}')" class="btn btn-secondary" title="{{ __('Download Invoice History') }}"><i class="fas fa-download"></i></button></td>
                                  </tr>
                                @endforeach

                                @endif
                            </tbody>
                          </table>
                    </div>

                </div>
            </div> --}}
    {{-- </div> --}}


    
    


<script type="text/javascript">
    function printInvoice(invoiceId) { 

        if (!invoiceId) {
            return
        }else{
            var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        
        let url = "{{ route('admin-branch.billing.print', ':invoiceId') }}";
        url = url.replace(':invoiceId', invoiceId); 

        iframe.src = url

        iframe.onload = function() {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };

        document.body.appendChild(iframe);
        }  
    }
</script>


@endsection
