@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('edit.module', ['module' => __('Harga Billing')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-6">
                        <form action="" method="post">
                            @csrf
                            @method('PUT')

                            <input type="hidden" value="{{ $prices->id }}" name="id">

                            <div class="form-group">
                                <label for="branches_type">{{ __('Tipe Lisensi') }}</label>

                                <input
                                    name="branches_type"
                                    type="text"
                                    class="form-control
                                    @error('branches_type') is-invalid @enderror"
                                    value="{{old('branches_type') ?: $prices->branches_type->name}}"
                                    readonly
                                >

                                @include('layouts.inputError', ['errorName' => 'branches_type'])
                            </div>

                            <div class="form-group">
                                <label for="queue_types">{{ __('Tipe Antrian') }}</label>

                                <input
                                    name="queue_types"
                                    type="text"
                                    class="form-control
                                    @error('queue_types') is-invalid @enderror"
                                    value="{{ 
                                                $prices->branches_type->is_appointment ? 'Appointment' : 
                                                ($prices->branches_type->is_direct_queue ? 'Onsite' : 'Lainnya') 
                                            }}"
                                    readonly
                                >

                                @include('layouts.inputError', ['errorName' => 'branches_type'])
                            </div>

                            <div class="form-group">
                                <label for="billing_types">Tipe Billing</label>
                                <select
                                    name="billing_types"
                                    id="billing_types"
                                    class="form-control"
                                    required
                                >
                                    <option value="lite" {{ $prices->billing_types == "lite" ? 'selected' : '' }}>Lite</option>
                                    <option value="premium" {{ $prices->billing_types == "premium" ? 'selected' : '' }}>Premium</option>
                                    <option value="custom" {{ $prices->billing_types == "custom" ? 'selected' : '' }}>Custom</option>
                                </select>

                                @include('layouts.inputError', ['errorName' => 'subscription_duration'])
                            </div>

                            

                           
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="prices">{{ __('Harga Billing') }} <small class="text-danger ml-2">*Dalam format rupiah</small></label>

                                <input
                                    name="prices"
                                    type="number"
                                    class="form-control
                                    @error('prices') is-invalid @enderror"
                                    value="{{old('prices') ?: $prices->prices}}"
                                    required
                                >

                                @include('layouts.inputError', ['errorName' => 'prices'])
                            </div>
                            <div class="form-group">
                                <label for="subscription_duration">Durasi Langganan</label>
                                <select
                                    name="subscription_duration"
                                    id="subscription_duration"
                                    class="form-control"
                                    required
                                >
                                    <option value="3"  {{ $prices->subscription_duration == 3 ? 'selected' : '' }}>3 Bulan</option>
                                    <option value="6" {{ $prices->subscription_duration == 6 ? 'selected' : '' }}>6 Bulan</option>
                                    <option value="12" {{ $prices->subscription_duration == 12 ? 'selected' : '' }}>12 Bulan</option>
                                </select>

                                @include('layouts.inputError', ['errorName' => 'subscription_duration'])
                            </div>
                            </div>
                            <button class="ml-3 btn btn-warning">{{ __('Update') }}</button>
                        </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
  
</script>
@endpush