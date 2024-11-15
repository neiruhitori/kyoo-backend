@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('create.module', ['module' => __('Harga Billing')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <div class="row">
                    <div class="col-md-6">
                        <form action="" method="post">
                            @csrf

                            <div class="form-group">
                                <label for="queue_types">Tipe Antrian</label>
                                <select
                                    name="queue_types"
                                    id="queue_types"
                                    class="form-control"
                                    required
                                >
                                    <option value="onsite" >Onsite</option>
                                    <option value="appointment">Appointment</option>
                                </select>

                                @include('layouts.inputError', ['errorName' => 'subscription_duration'])
                            </div>


                            <div class="form-group">
                                <label for="branches_types">{{ __('Jenis Lisensi') }}</label>

                                <input
                                    name="branches_types"
                                    id="branches_types"
                                    type="text"
                                    class="form-control
                                    @error('branches_types') is-invalid @enderror"
                                    value=""
                                    readonly
                                >

                                @include('layouts.inputError', ['errorName' => 'branches_types'])
                            </div>

                           

                            <div class="form-group">
                                <label for="billing_types">Tipe Billing</label>
                                <select
                                    name="billing_types"
                                    id="billing_types"
                                    class="form-control"
                                    required
                                >
                                    <option value="lite" >Lite</option>
                                    <option value="premium">Premium</option>
                                    <option value="custom" >Custom</option>
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
                                    <option value="3">3 Bulan</option>
                                    <option value="6">6 Bulan</option>
                                    <option value="12">12 Bulan</option>
                                </select>

                                @include('layouts.inputError', ['errorName' => 'subscription_duration'])
                            </div>
                            </div>
                            <button class="ml-3 btn btn-warning">{{ __('Tambah') }}</button>
                        </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    const selectedQueueType = document.getElementById('queue_types');
   const branchesTypeField = document.getElementById('branches_types');
   
   function changeTypes() {
       let type = selectedQueueType.value;  
   
       if (type == 'onsite') {
           branchesTypeField.value = 'Premium Direct Queue'; 
       } else if (type == 'appointment') {
           branchesTypeField.value = 'Premium Appointment Services'; 
       } else {
           branchesTypeField.value = '';  
       }
   }
   
   selectedQueueType.addEventListener('change', changeTypes);
   changeTypes();

   </script>
@endsection


