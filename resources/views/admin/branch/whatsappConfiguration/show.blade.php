@extends('layouts.app')

@section('content')
    <h3 class="mb-3">{{ $branch->name }}</h3>

    @include('layouts.alert')

    <form action="{{ route('admin.whatsappConfiguration.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Whatsapp</h6>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="license-cfg-item mb-2">
                            <label for="none" class="font-weight-bold">None</label>
                            <div>
                                <input
                                    type="radio"
                                    name="whatsapp_type"
                                    id="none"
                                    value="none"
                                    class="license-checkbox form-control"
                                    {{ $branch_configuration->whatsapp_type == 'none' ? 'checked' : '' }}
                                    onchange="changeType(this)"
                                >
                            </div>
                        </div>
                        <div for="wa_kyoo" class="license-cfg-item mb-2">
                            <label class="font-weight-bold">WA Kyoo</label>
                            <div>
                                <input
                                    type="radio"
                                    name="whatsapp_type"
                                    id="wa_kyoo"
                                    value="wa_kyoo"
                                    class="license-checkbox form-control"
                                    {{ $branch_configuration->whatsapp_type == 'wa_kyoo' ? 'checked' : '' }}
                                    onchange="changeType(this)"
                                >
                            </div>
                        </div>
                        <div for="official_wa_branch" class="license-cfg-item mb-2">
                            <label class="font-weight-bold">Official WA Branch</label>
                            <div>
                                <input
                                    type="radio"
                                    name="whatsapp_type"
                                    id="official_wa_branch"
                                    value="official_wa_branch"
                                    class="license-checkbox form-control"
                                    {{ $branch_configuration->whatsapp_type == 'official_wa_branch' ? 'checked' : '' }}
                                    onchange="changeType(this)"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6  {{ $branch_configuration->whatsapp_type !== 'wa_kyoo' ? 'd-none' : ''}}" id="secret_token">
                        <div class="form-group">
                            <label for="api_wa">{{ __('SECRET KEY') }}</label>
                            <input
                                name="secret_key"
                                id="secret_key"
                                type="text"
                                value="{{ $branch_configuration->whatsapp_type === 'wa_kyoo' ? $branch_configuration->api_token : '' }}"
                                class="form-control @error('secret_key') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="col-md-6 {{ $branch_configuration->whatsapp_type !== 'official_wa_branch' ? 'd-none' : ''}}" id="api">
                        <div class="form-group">
                            <label for="api_wa">{{ __('API WA') }}</label>
                            <input
                                name="api_wa"
                                id="api_wa"
                                type="text"
                                value="{{ $branch_configuration->api_wa }}"
                                class="form-control @error('api_wa') is-invalid @enderror">
                        </div>
                        <div class="form-group">
                            <label for="api_token">{{ __('API TOKEN') }}</label>
                            <input
                                name="api_token"
                                id="api_token"
                                type="text"
                                value="{{ $branch_configuration->whatsapp_type === 'official_wa_branch' ? $branch_configuration->api_token : '' }}"
                                class="form-control @error('api_token') is-invalid @enderror">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-warning">Simpan</button>
    </form>

    <script>
        function changeType(input) {
            const { value } = input;
            const apiDiv = document.getElementById("api");
            const secretToken = document.getElementById("secret_token");

            if (value == 'official_wa_branch') {
                secretToken.classList.add("d-none")
                const tokens = secretToken.querySelectorAll("input");
                tokens.forEach(function(token) {
                    token.removeAttribute("required", "required");
                });
                apiDiv.classList.remove("d-none")
                const inputs = apiDiv.querySelectorAll("input");
                inputs.forEach(function(input) {
                    input.setAttribute("required", "required");
                });
            }else if(value == "wa_kyoo"){
                apiDiv.classList.add("d-none")
                const inputs = apiDiv.querySelectorAll("input");
                inputs.forEach(function(input) {
                    input.removeAttribute("required");
                });
                secretToken.classList.remove("d-none")
                const tokens = secretToken.querySelectorAll("input");
                tokens.forEach(function(token) {
                    token.setAttribute("required", "required");
                });
            }
             else {
                secretToken.classList.add("d-none")
                const tokens = secretToken.querySelectorAll("input");
                tokens.forEach(function(token) {
                    token.removeAttribute("required", "required");
                });
                apiDiv.classList.add("d-none")
                const inputs = apiDiv.querySelectorAll("input");
                inputs.forEach(function(input) {
                    input.removeAttribute("required");
                });
            }
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

    .license-checkbox {
        width: 1.25rem;
        height: 1.25rem;
    }
</style>
@endpush
