@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 text-primary">
            Daftar Sub Layanan <strong>{{ $parentService->name }}</strong>
        </h6>
    </div>

    <div class="card-body">
        @include('layouts.alert')

        <div class="mb-3">
            <a href="{{ route('admin-branch.branch-configuration.service.sub-service.create', $parentService->id) }}" class="btn btn-primary">
                Tambah Sub Layanan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sub Layanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td>
                                <form action="{{ route('admin-branch.branch-configuration.service.sub-service.destroy', [$parentService->id, $service->id]) }}" method="post" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        data-toggle="tooltip"
                                        data-placement="bottom"
                                        title="Hapus Sub Layanan"
                                    >
                                        <i class="fas fa-fw fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection