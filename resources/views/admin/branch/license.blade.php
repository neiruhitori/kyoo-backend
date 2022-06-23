@extends('layouts.app')

@section('content')
    <h3 class="mb-3">{{ $branch->name }}</h3>

    @include('layouts.alert')

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
                                    {{ $branch_type->id == $branch_license->id ? 'selected' : '' }}
                                >
                                    {{ $branch_type->code }} - {{ $branch_type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="license-cfg-item">
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
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Aktivasi Fitur Tambahan</h6>
            </div>

            <div class="card-body">
                @if (!$branch_license->is_premium)
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
                                {{ !$branch_license->is_premium ? 'disabled' : '' }}
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <button type="submit" class="btn btn-warning">Simpan</button>
    </form>
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