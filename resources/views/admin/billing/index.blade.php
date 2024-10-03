@extends('layouts.app')

@push('css')
<link href="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
<style>
    .img-size-constraint {
        width: auto;
        height: auto;
        max-height: 40px;
        max-width: 40px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('list.module', ['module' => __('Invoice')]) }}
                </h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                    <div class=" row">
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Nama Branch') }}</th>
                                                <th>{{ __('Tanggal Invoice') }}</th>
                                                <th>{{ __('Nominal') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice as $inv)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$inv->branch->name}}</td>
                                                <td>{{\Carbon\Carbon::parse($inv->created_at)->translatedFormat('d F Y H:i')}} </td>
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
                                                <td>
                                                    <button
                                                        onclick="printInvoice('{{ $inv->id_invoice }}')"
                                                        class="btn btn-secondary"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Unduh Riwayat Invoice"
                                                    >
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('js')
    <script src="{{asset('admin/vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/js/demo/datatables-demo.js')}}"></script>

    <script type="text/javascript">
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })

        function printInvoice(invoiceId) { 

                if (!invoiceId) {
                    return
                }else{
                    var iframe = document.createElement('iframe');
                iframe.style.display = 'none';

                let url = "{{ route('admin.billing.print', ':invoiceId') }}";
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
    @endpush
