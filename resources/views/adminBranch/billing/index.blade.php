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



    <div class="mb-3 mx-3">
        <h5 class="font-weight-bold text-primary mb-0">
            Menu Billing
        </h5>
    </div>

<div class="card shadow mb-4">
    <div class="card-header pb-0">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
             <a class="nav-link active px-5" id="license-tab" data-toggle="tab" href="#license" role="tab" aria-controls="license" aria-selected="true">License</a>
            </li>
            <li class="nav-item" role="presentation">
             <a class="nav-link px-5" id="invoice-tab" data-toggle="tab" href="#invoice" role="tab" aria-controls="invoice" aria-selected="false">Invoice</a>
            </li>
            </ul>
    </div>
    <div class="card-body">     
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="license" role="tabpanel" aria-labelledby="license-tab">
                    @if (Auth::user()->Branch->BranchType->is_premium)
                    <div class="mx-4 my-4">
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;">Jenis License:</h6>
                            <h6><b>{{ Auth::user()->Branch->BranchType->name }}</b></h6>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;">Masa Aktif:</h6>
                            {{-- format tanggal d-M-Y   --}}
                            <h6> <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->updated_at)->translatedFormat('d F Y') }}</b> - 
                                <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->license_expiration_date)->translatedFormat('d F Y') }}</b>
                            </h6>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <h6 style="min-width: 150px;">Fitur:</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-primary mb-2">{{ Auth::user()->Branch->max_counter }} Meja</button>
                                    <button class="btn btn-primary mb-2">{{ Auth::user()->Branch->max_queue }} Antrian</button>

                                    @foreach ($features as $val)

                                    <button class="btn btn-primary mb-2">{{ $val->additionalFeature->name }}</button>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mx-4 my-4">
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;">Jenis License:</h6>
                            <h6><b>Trial</b></h6>
                            <a href="{{ route('admin-branch.subscription') }}" class="ml-3 btn btn-warning">Daftar Berlangganan KYOO</a>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;">Masa Aktif:</h6>
                            <h6> <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->updated_at)->translatedFormat('d F Y') }}</b> - 
                                <b>{{ \Carbon\Carbon::parse(Auth::user()->Branch->license_expiration_date)->translatedFormat('d F Y') }}</b>
                            </h6>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h6 style="min-width: 150px;">Fitur:</h6>
                            <div>
                                <button class="btn btn-primary mb-2">{{ Auth::user()->Branch->max_counter }} Meja</button>
                                    <button class="btn btn-primary mb-2">{{ Auth::user()->Branch->max_queue }} Antrian</button>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                </div>


                <div class="tab-pane fade mx-2 my-4" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                    <div class="mx-4 my-4">
                        <h4 class="mb-3">Invoice History</h4>
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Deskripsi</th>
                                <th scope="col">Jumlah Nominal</th>
                                <th scope="col">Status</th>
                                <th scope="col">Opsi</th>
                              </tr>
                            </thead>
                            <tbody>
                                @if ($invoice->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data invoice.</td>
                                </tr>    
                                @else
                                @foreach ($invoice as $inv)
                                <tr>
                                    <th>{{ \Carbon\Carbon::parse($inv->created_at)->translatedFormat('d F Y') }}</th>
                                    <td>{{ $inv->description }}</td>
                                    <td>Rp. {{ number_format($inv->amount, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($inv->status == "PAID")
                                        <b><span class="badge badge-success">{{$inv->status}}</span></b>
                                        @elseif($inv->status == "PENDING")
                                        <b><span class="badge badge-warning text-dark">{{$inv->status}}</span></b>
                                        @else
                                        <b><span class="badge badge-danger">{{$inv->status}}</span></b>
                                        @endif 
                                    </td>
                                    {{-- <td><a href="{{ route('admin-branch.billing.print', $inv->id_invoice) }}" target="_blank" class="btn btn-secondary">Print</a></td> --}}
                                    <td><button onclick="printInvoice('{{ $inv->id_invoice }}')" class="btn btn-secondary" title="Unduh Riwayat Invoice"><i class="fas fa-download"></i></button></td>
                                  </tr>
                                @endforeach

                                @endif
                            </tbody>
                          </table>
                    </div>

                </div>
            </div>
    </div>


    
    
</div>

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
