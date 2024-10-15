@extends('layouts.app')

@section('content')
    <h3 class="mb-3">{{ $branch->name }}</h3>

    @include('layouts.alert')

    @if ($selected_features->contains('feature_id', 8))
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Endpoint untuk Webhook</h6>
        </div>

        <div class="card-body">
            {{-- <form action=""> --}}
            <div class="row">
                    <div class="col-md-3">
                        <label for="endpoint" class="font-weight-bold">{{ __('Endpoint Webhook') }}</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group mb-2">
                            <div>
                                <input
                                    type="text"
                                    name="endpoint"
                                    id="endpoint"
                                    class="form-control"
                                    min="0"
                                    value=""
                                    placeholder="http://your.api.endpoint"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-warning">Simpan</button>
                    </div>
                </div>
        {{-- </form> --}}
        </div>
    </div>
                    
    @endif
    @if ($selected_features->contains('feature_id', 8))
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Webhook Secret Key</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.branch.license.generateToken', $branch->id) }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <label for="endpoint" class="font-weight-bold">{{ __('Secret Key') }}</label>
                    </div>
                    @if (!empty($secret_token))
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <div>
                                <input
                                    type="text"
                                    id="secret_key"
                                    class="form-control"
                                    value="{{ $secret_token->secret_token }}"
                                    readonly
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" onclick="copyText()" class="btn btn-secondary">Copy Token</button>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Regenerate Token</button>
                    </div>
                @else
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Generate Token</button>
                    </div>
                @endif
                </div>
        </form>
        </div>
    </div>
                    
    @endif

    <form action="{{ route('admin.branch.license.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Lisensi</h6>
            </div>

            <div class="card-body">
                <div class="license-cfg-item mb-2">
                    <label for="branch_type_id" class="font-weight-bold">Jenis Lisensi</label>
                    <div>
                        <select
                            name="branch_type_id"
                            id="branch_type_id"
                            class="form-control @error('branch_type_id') is-invalid @enderror"
                            required
                        >
                            @foreach ($branch_types as $branch_type)
                                <option
                                    value="{{ $branch_type->id }}"
                                    {{ $branch_license && $branch_type->id == $branch_license->id ? 'selected' : '' }}
                                >
                                    {{ $branch_type->code }} - {{ $branch_type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="license-cfg-item mb-2">
                    <label for="max_counter" class="font-weight-bold">Maks. Counter</label>
                    <div>
                        <input
                            type="number"
                            name="max_counter"
                            id="max_counter"
                            class="form-control"
                            min="0"
                            value="{{ $branch->max_counter }}"
                        >
                    </div>
                </div>
                <div class="license-cfg-item mb-2">
                    <label for="max_queue" class="font-weight-bold">{{ __('Max. Queue') }}</label>
                    <div>
                        <input
                            type="number"
                            name="max_queue"
                            id="max_queue"
                            class="form-control"
                            min="0"
                            value="{{ $branch->max_queue }}"
                        >
                    </div>
                </div>
                <div class="license-cfg-item">
                    <label for="license_expiration_date" class="font-weight-bold">Tambah Masa Trial</label>
                    <div>
                        <input
                            type="datetime-local"
                            name="license_expiration_date"
                            id="license_expiration_date"
                            class="form-control"
                            {{-- min="{{ $branch->license_expiration_date }}" --}}
                            value="{{ $branch->license_expiration_date }}"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Aktivasi Fitur Tambahan</h6>
            </div>

            <div class="card-body">
                @if (!$branch_license || !$branch_license->is_premium)
                    <p class="text-danger">Anda memerlukan lisensi premium untuk mengaktifkan fitur ini.</p>
                @endif

                @foreach ($features as $feature)
                    <div class="license-cfg-item mb-2">
                        <label for="{{ $feature->code }}" class="font-weight-bold">{{ $feature->name }}</label>
                        <div>
                            <input
                                type="checkbox"
                                name="feature_name[]"
                                id="{{ $feature->code }}"
                                class="license-checkbox form-control"
                                value="{{ $feature->id }}"
                                autocomplete="off"
                                {{ sizeof($selected_features->where('feature_id', $feature->id)) ? 'checked' : '' }}
                                {{ !$branch_license || !$branch_license->is_premium ? 'disabled' : '' }}
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-warning">Simpan</button>
    </form>
   
    <script>
  function copyText() {
        var copyText = document.getElementById("secret_key");
        
        copyText.select();
        copyText.blur();
        copyText.setSelectionRange(0, 99999); 
        document.execCommand("copy");
        window.getSelection().removeAllRanges();
       
    }
    </script>

  
@endsection

@push('css')
<style>
    .license-cfg-item {
        display: flex;
        gap: 1rem;
    }

    .license-cfg-item label {
        display: block;
        width: 100%;
        max-width: 240px;
    }

    .license-cfg-item div {
        width: 100%;
        max-width: 240px;
    }
    .license-endpoint-item div {
        width: 100%;
        max-width: 500px;
    }

    .license-checkbox {
        width: 1.25rem;
        height: 1.25rem;
    }
</style>
@endpush
