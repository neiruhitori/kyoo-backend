@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4" style="max-width: 600px;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Tambah Sub Layanan
            </h6>
        </div>

        <div class="card-body">
            @include('layouts.alert')

            <form action="{{ route('admin-branch.branch-configuration.service.sub-service.store', $parentService->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Layanan</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>

                    @include('layouts.inputError', ['errorName' => 'name'])
                </div>

                <a
                    href="{{ route('admin-branch.branch-configuration.service.sub-service.index', $parentService->id) }}"
                    class="btn btn-secondary mr-1"
                >Kembali</a>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection