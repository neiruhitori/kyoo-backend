@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ __('create.module', ['module' => __('Harga Item')]) }}
                </h6>
            </div>
            @csrf
            <div class="card-body">
                @include('layouts.alert')
                <form action="" method="post">
                <div class="row">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_name">Nama Item</label>
                                    
                                        <input
                                        name="item_name"
                                        type="text"
                                        class="form-control
                                        @error('item_name') is-invalid @enderror"
                                        required
                                        value="{{ $items->item_name ? $items->item_name : '' }}"
                                    >
    
                                    @include('layouts.inputError', ['errorName' => 'item_name'])
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                <label for="prices">{{ __('Harga Item (Oversea)') }} <small class="text-danger ml-2">*Dalam format USD</small></label>

                                    <input
                                        name="en_prices"
                                        type="number"
                                        class="form-control
                                        @error('prices') is-invalid @enderror"
                                        required
                                        value="{{ $items->en_prices ? $items->en_prices : 0 }}"
                                    >

                                    @include('layouts.inputError', ['errorName' => 'prices'])
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                <label for="prices">{{ __('Harga Item') }} <small class="text-danger ml-2">*Dalam format rupiah</small></label>

                                    <input
                                        name="prices"
                                        type="number"
                                        class="form-control
                                        @error('prices') is-invalid @enderror"
                                        required
                                        value="{{ $items->prices ? $items->prices : 0 }}"
                                    >

                                    @include('layouts.inputError', ['errorName' => 'prices'])
                                </div>
                                
                           
                            </div>
                        </div>
                        <button class="btn btn-warning">{{ __('Tambah') }}</button>
                    </form>
        </div>
    </div>
</div>

@endsection


