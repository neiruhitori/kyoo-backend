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
                    {{ __('list.module', ['module' => __('Harga Lisensi')]) }}
                </h6>
            </div>
            <div class="card-body">
                @include('layouts.alert')
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('admin.billing-prices.create') }}" class="btn btn-primary">Tambah Billing</a>
                        </div>
                    </div>
                    <div class=" row">
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Nama Lisensi') }}</th>
                                                <th>{{ __('Tipe Antrian') }}</th>
                                                <th>{{ __('Tipe Lisensi') }}</th>
                                                <th>{{ __('Nominal') }}</th>
                                                <th>{{ __('Nominal (USD)') }}</th>
                                                <th>{{ __('Lama Langganan') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($prices as $p)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$p->branches_type->name}}</td>
                                                <td>
                                                    @if ($p->branches_type->is_direct_queue)
                                                    <b>Onsite</b>
                                                    @elseif($p->branches_type->is_appointment)
                                                    <b>Appointment</b>
                                                    @else
                                                    <b>Exhibition</b>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($p->billing_types == "lite")
                                                    <h5><span class="badge badge-primary">Lite</span></h5>
                                                    @elseif($p->billing_types == "premium")
                                                    <h5><span class="badge badge-warning text-dark">Premium</span></h5>
                                                    @else
                                                    <h5><span class="badge badge-secondary">Custom</span></h5>
                                                    @endif 
                                                </td>
                                                <td>Rp. {{ number_format($p->prices, 2, ',', '.') }}</td>
                                                <td>USD ${{ $p->en_prices ?: 0}}</td>
                                                <td>
                                                  {{ $p->subscription_duration }} Bulan
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.billing-prices.update', $p->id) }}"
                                                        class="btn btn-warning"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Edit Harga Lisensi"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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

    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    @endpush
