@extends('layouts.app')

@section('content')
    <h3 class="mb-3">{{ $branch->name }}</h3>

    @include('layouts.alert')

    <form action="{{ route('admin.gtmConfiguration.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Konfigurasi Google Tag Manager</h6>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6" id="api">
                        <div class="form-group">
                            <label for="gtm_script">{{ __('Script Google Tag Manager') }}</label>
                            <input
                                name="gtm_script"
                                id="gtm_script"
                                type="text"
                                value="{{ $branch_configuration->gtm_script }}"
                                class="form-control @error('gtm_script') is-invalid @enderror">
                        </div>
                        <div class="form-group">
                            <label for="gtm_noscript">{{ __('Noscript Google Tag Manager') }}</label>
                            <input
                                name="gtm_noscript"
                                id="gtm_noscript"
                                type="text"
                                value="{{ $branch_configuration->gtm_noscript }}"
                                class="form-control @error('gtm_noscript') is-invalid @enderror">
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

            if (value == 'official_wa_branch') {
                apiDiv.classList.remove("d-none")
                const inputs = apiDiv.querySelectorAll("input");
                inputs.forEach(function(input) {
                    input.setAttribute("required", "required");
                });
            } else {
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
