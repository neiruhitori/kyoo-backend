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
    .branch-config-guide__premium__card-title {
        color: #0C61A1;
    }
    .branch-config-guide__premium__wrapper-title {
        display: grid;
        justify-content: center;
        justify-items: center;
        text-align: -webkit-center;
        font-size: 14px;
    }
    .branch-config-guide__premium__title {
        color: #0C61A1;
        font-weight: bold;
        font-size: 20px;
    }
    .branch-config-guide__premium__wrapper-icons {
        display: grid;
        align-items: center;
        justify-items: center;
        text-align: -webkit-center;
    }
    .branch-config-guide__premium__image-icon {
        width: 25px;
        height: 25px;
    }
    .branch-config-guide__premium__icons {
        padding: 18px;
        background-image: linear-gradient(to right, #0C61A1 , #4F7BCF);
        border-radius: 50%;
        color: #FFF;
    }
    .branch-config-guide__premium__icons-step {
        font-size: 12px;
        font-weight: 700;
        color: #3F67B5;
    }
    .branch-config-guide__premium__icons-label {
        font-size: 14px;
        font-weight: 700;
        color: #0C61A1;
    }

    .branch-config-guide__premium__icons-button {
        color: #fff;
        background-color: #0C61A2;
        font-weight: 500;
        font-size: 12px;
        border-color: #0C61A1;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold
                        {{ Auth::user()->Branch->queue_type === 'onsite' && Auth::user()->Branch->BranchType->is_premium ? 'branch-config-guide__premium__card-title' : 'text-primary' }}"
                    >
                        {{ __('How to Configure Queue') }}
                    </h6>
                </div>
 
                @if (Auth::user()->Branch->queue_type === 'onsite' && Auth::user()->Branch->BranchType->is_premium)
                    <div class="card-body">
                        <div class="mb-5">
                            <div class="branch-config-guide__premium__wrapper-title mb-5">
                                <p class="branch-config-guide__premium__title">
                                    Konfigurasi Utama antrian
                                </p>
                                <span>Berikut merupakan tahapan yang anda dapat lakukan sebagai admin cabang</span>
                                <span>agar anda dan pelanggan anda dapat menggunakan platform Antrian KYOO.</span>
                            </div>

                            <div class="row">
                                <div class="col-lg-2 col-md-6 col-sm-12 offset-lg-1 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/apartment-config.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 branch-config-guide__premium__icons-step">
                                        Langkah 1
                                    </span>
                                    <span class="mt-2 mb-4 branch-config-guide__premium__icons-label">
                                        Anda perlu mengubah informasi tentang kantor cabang
                                    </span>
                                    <a href="{{ route('admin-branch.branch-information.profile') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Profil Cabang
                                    </a>
                                </div>
                            
                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/location-config.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 branch-config-guide__premium__icons-step">
                                        Langkah 2
                                    </span>
                                    <span class="mt-2 mb-4 branch-config-guide__premium__icons-label">
                                        Anda perlu mengubah lokasi kantor cabang anda
                                    </span>
                                    <a href="{{ route('admin-branch.branch-information.location') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Lokasi Cabang
                                    </a>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/branch-config.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 branch-config-guide__premium__icons-step">
                                        Langkah 3
                                    </span>
                                    <span class="mt-2 mb-4 branch-config-guide__premium__icons-label">
                                        Anda perlu mengubah jenis dan nama layanan kepada pelanggan
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Jenis Layanan
                                    </a>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/date-config.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 branch-config-guide__premium__icons-step">
                                        Langkah 4
                                    </span>
                                    <span class="mt-2 mb-4 branch-config-guide__premium__icons-label">
                                        Anda perlu mengubah jadwal buka dan tutup layanan
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.schedule.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Jadwal Layanan
                                    </a>
                                </div>

                                <div class="col-lg-2 col-md-12 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/cs-config.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 branch-config-guide__premium__icons-step">
                                        Langkah 5
                                    </span>
                                    <span class="mt-2 mb-4 branch-config-guide__premium__icons-label">
                                        Anda perlu mengubah akses petugas layanan
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Petugas Layanan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="branch-config-guide__premium__wrapper-title mb-5">
                                <p class="branch-config-guide__premium__title">
                                    Konfigurasi Opsional
                                </p>
                                <span>Anda juga bisa merubah beberapa operasional yang ada.</span>
                            </div>

                            <div class="row">
                                <div class="col-lg-2 col-md-6 col-sm-12 offset-lg-2 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/apartment.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 mb-3 branch-config-guide__premium__icons-label">
                                        Nama Departement
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Menu Departemen
                                    </a>
                                </div>
                            
                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/layanan.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 mb-3 branch-config-guide__premium__icons-label">
                                        Nama Layanan
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Menu Departemen
                                    </a>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/meja.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 mb-3 branch-config-guide__premium__icons-label">
                                        Nama Meja
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.workstation.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Menu Meja
                                    </a>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-12 mb-4 branch-config-guide__premium__wrapper-icons">
                                    <div class="branch-config-guide__premium__icons">
                                        <img
                                            class="branch-config-guide__premium__image-icon"
                                            src="{{ asset('img/branch-configuration/petugas.svg') }}"
                                        >
                                    </div>
                                    <span class="mt-3 mb-3 branch-config-guide__premium__icons-label">
                                        Nama User Petugas
                                    </span>
                                    <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm branch-config-guide__premium__icons-button ml-2">
                                        Menu Petugas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body">
                        <p class="mb-5">
                            Berikut merupakan tahapan yang anda dapat lakukan sebagai admin cabang agar Anda dan Pelanggan Anda dapat menggunakan platform Antrian KYOO.
                        </p>
                        
                        <div class="mb-4">
                            <h5 class="font-weight-bold mb-4">Konfigurasi Utama Antrian</h5>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">1</span>

                                <p>
                                    <strong>Langkah 1</strong>, Anda perlu mengubah informasi tentang kantor Cabang anda di <a href="{{ route('admin-branch.branch-information.profile') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Informasi Profil Cabang</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">2</span>

                                <p>
                                    <strong>Langkah 2</strong>, Anda perlu mengupdate lokasi kantor cabang anda di <a href="{{ route('admin-branch.branch-information.location') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Informasi Lokasi Cabang</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">3</span>

                                <p>
                                    <strong>Langkah 3</strong>, Anda perlu mengupdate jenis dan nama layanan kepada pelanggan di <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Jenis Layanan</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">4</span>

                                <p>
                                    <strong>Langkah 4</strong>, Anda perlu mengupdate jadwal Buka dan Tutup Layanan Anda di <a href="{{ route('admin-branch.branch-configuration.schedule.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Jadwal Layanan</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">5</span>

                                <p>
                                    <strong>Langkah 5</strong>, Anda perlu mengupdate akses Petugas Layanan di <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Petugas Layanan</a>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="font-weight-bold mb-4">Konfigurasi Opsional</h5>

                            <p>Anda juga dapat merubah:</p>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">1</span>

                                <p>
                                    Nama Departemen dari menu <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Departemen</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">2</span>

                                <p>
                                    Nama Layanan dari menu <a href="{{ route('admin-branch.branch-configuration.department.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Departemen</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">3</span>

                                <p>
                                    Nama Meja dari menu <a href="{{ route('admin-branch.branch-configuration.workstation.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Meja</a>
                                </p>
                            </div>

                            <div class="number-list">
                                <span class="number-indicator bg-primary mr-3">4</span>

                                <p>
                                    Nama user petugas layanan dari menu <a href="{{ route('admin-branch.branch-configuration.user.index') }}" target="__blank" class="btn btn-sm btn-warning ml-2">Petugas Layanan</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection