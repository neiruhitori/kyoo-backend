@extends('layouts.app')

@push('css')
<style>
    .number-list {
        display: flex;
    }

    .number-list .number-indicator {
        font-weight: bold;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        line-height: 0px;
        height: 1.25rem;
        width: 1.25rem;
        margin: 4px;
        background-color: black;
        color: white;
        border-radius: 999999999px;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Configure Queue') }}</h6>
                </div>
 
                <div class="card-body">
                    <p class="mb-5">
                        Berikut merupakan tahapan yang anda dapat lakukan sebagai admin cabang agar Anda dan Pelanggan Anda dapat menggunakan platform Antrian KYOO.
                    </p>
                    
                    <div class="mb-4">
                        <h5 class="text-dark font-weight-bold mb-4">Konfigurasi Utama Antrian</h5>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">1</span>

                            <p>
                                <strong>Langkah 1</strong>, Anda perlu mengubah informasi tentang kantor Cabang anda di <a href="{{ route('adminBranch.branch.profile') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Informasi Profil Cabang</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">2</span>

                            <p>
                                <strong>Langkah 2</strong>, Anda perlu mengupdate lokasi kantor cabang anda di <a href="{{ route('adminBranch.branch.location') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Informasi Lokasi Cabang</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">3</span>

                            <p>
                                <strong>Langkah 3</strong>, Anda perlu mengupdate jenis dan nama layanan kepada pelanggan di <a href="{{ route('adminBranch.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Jenis Layanan</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">4</span>

                            <p>
                                <strong>Langkah 4</strong>, Anda perlu mengupdate jadwal Buka dan Tutup Layanan Anda di <a href="{{ route('adminBranch.schedule.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Jadwal Layanan</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">5</span>

                            <p>
                                <strong>Langkah 5</strong>, Anda perlu mengupdate akses Petugas Layanan di <a href="{{ route('adminBranch.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Petugas Layanan</a>
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h5 class="text-dark font-weight-bold mb-4">Konfigurasi Opsional</h5>

                        <p>Anda juga dapat merubah:</p>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">1</span>

                            <p>
                                Nama Departemen dari menu <a href="{{ route('adminBranch.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Departemen</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">2</span>

                            <p>
                                Nama Layanan dari menu <a href="{{ route('adminBranch.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Departemen</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">3</span>

                            <p>
                                Nama Meja dari menu <a href="{{ route('adminBranch.workstation.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Meja</a>
                            </p>
                        </div>

                        <div class="number-list">
                            <span class="number-indicator bg-primary mr-3">4</span>

                            <p>
                                Nama user petugas layanan dari menu <a href="{{ route('adminBranch.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Petugas Layanan</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection