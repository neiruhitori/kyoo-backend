@extends('layouts.app')

@push('css')
<style>
    .guide-list-indicator {
        width: 10px;
        height: 10px;
        border-radius: 999999px;
        display: block;
        background: black;
        margin: 4px;
        flex-shrink: 0;
        margin-right: 1rem;
    }

    .inline {
        display: flex;
    }

    .flex-1 {
        flex: 1 1 0%;
    }

    .custom-table {
        border-collapse: separate;
        border: 1px solid #dee2e6;
        border-spacing: 0;
        border-radius: 0.5rem;
        width: 100%;
    }

    .custom-table th, .custom-table td {
        padding: 0.75rem;
        box-sizing: border-box;
    }

    .custom-table tr th,
    .custom-table tr:not(:last-child) td {
        border-bottom: 1px solid #dee2e6;
    }

    .custom-table tr th:not(:last-child),
    .custom-table tr td:not(:last-child) {
        border-right: 1px solid #dee2e6;
    }

    .row-group td {
        border-top: 2px solid #28a745;
        border-bottom: 2px solid #28a745 !important;
        border-right: none !important;
        background-color: #d4edda;
        color: black;
    }

    .row-group td:first-child {
        border-left: 2px solid #28a745;
    }

    .row-group-multi {
        background-color: #d4edda;
    }

    .row-group-multi td {
        border-right: none !important;
        color: black;
    }

    .row-group-multi td:last-child {
        border-right: 2px solid #28a745 !important;
    }

    .row-group-multi:nth-child(1) td {
        border-top: 2px solid #28a745;
    }

    .row-group-multi:nth-child(2) td {
        border-bottom: 2px solid #28a745 !important;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header p-4">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('How to Use Sevice Time Slot') }}</h6>
                </div>

                <div class="card-body p-4">
                    <p class="mb-3">
                        Berikut penduan untuk menggunakan Slot Waktu:
                    </p>

                    <div class="mb-4">
                        <div class="inline">
                            <span class="guide-list-indicator bg-primary"></span>
                            <p>
                                Apabila misal dalam 1 hari, sebuah service memiliki 2 periode jam untuk melayani pelanggan. Misal, seorang dokter praktik buka di hari Senin dengan jam praktik jam 09.00-12.00 dan kemudian di jam 15.00-17.00. Perlu diperhatikan bahwa jadwal waktu layanan paling awal di pengaturan timeslot tidak boleh lebih kecil dari jam buka layanan kantor cabang dan periode paling akhir jam layanan tidak boleh lebih besar dari jam tutup layanan kantor cabang di menu Schedule.
                            </p>
                        </div>

                        <div class="inline">
                            <span class="guide-list-indicator bg-primary"></span>
                            <p>
                                Mengatur jumlah junjungan pelanggan dalam suatu periode waktu/timeslot. Misal untuk contoh praktir dokter diatas, di jam kunjungan 09.00-12.00 diatur jumlah maksimal pasien 100 orang dan untuk layanan 15.00-17.00 diatur sejumlah maksimal pasien adalah 200 orang.
                            </p>
                        </div>
                    </div>

                    <div class="inline mb-4">
                        <div class="flex-1">
                            <h5 class="mb-3 font-weight-bold">Pengaturan Jadwal</h5>

                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th class="text-center">Jam Buka Kantor Cabang</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="row-group">
                                        <td>Senin</td>
                                        <td class="text-center">09.00 sd. 17.00</td>
                                    </tr>
                                    <tr>
                                        <td>Selasa</td>
                                        <td class="text-center">09.00 sd. 17.00</td>
                                    </tr>
                                    <tr>
                                        <td>Rabu</td>
                                        <td class="text-center">09.00 sd. 12.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex-1 ml-4">
                            <h5 class="mb-3 font-weight-bold">Pengaturan Slot Waktu</h5>

                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Nama Layanan</th>
                                        <th>Hari</th>
                                        <th class="text-center">Slot Waktu</th>
                                        <th class="text-right">Maks. Pelanggan</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr class="row-group-multi">
                                        <td>Praktik Dokter A</td>
                                        <td>Senin</td>
                                        <td class="text-center">09.00 - 12.00</td>
                                        <td class="text-right">100</td>
                                    </tr>
                                    <tr class="row-group-multi">
                                        <td>Praktik Dokter A</td>
                                        <td>Senin</td>
                                        <td class="text-center">15.00 - 17.00</td>
                                        <td class="text-right">200</td>
                                    </tr>
                                    <tr>
                                        <td>Praktik Dokter B</td>
                                        <td>Selasa</td>
                                        <td class="text-center">09.00 - 17.00</td>
                                        <td class="text-right">700</td>
                                    </tr>
                                    <tr>
                                        <td>Praktik Dokter C</td>
                                        <td>Rabu</td>
                                        <td class="text-center">09.00 - 12.00</td>
                                        <td class="text-right">200</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p>
                        Diatas adalah gambaran contoh konfigurasi di pengaturan jadwal dan pengaturan slot waktu.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection