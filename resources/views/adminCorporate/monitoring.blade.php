@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3>Monitoring Terpusat</h3>
</div>

<div class="card">
    <div class="card-body">
        <form id="filterForm" class="mb-4">
            <div class="form-row align-items-end">
                <div class="col-auto">
                    <label for="branchId">Cabang</label>
                    <select class="form-control" id="branchId" style="width: 200px;" autocomplete="off">
                        <option selected disabled></option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Nama Cabang</th>
                        <th rowspan="2" class="align-middle text-right">Menunggu</th>
                        <th rowspan="2" class="align-middle text-right">Dilayani</th>
                        <th rowspan="2" class="align-middle text-right">Tidak Hadir</th>
                        <th colspan="3" class="text-center">Waktu Tunggu</th>
                        <th colspan="3" class="text-center">Waktu Melayani</th>
                    </tr>

                    <tr>
                        {{-- Waktu Tunggu Child Header --}}
                        <th class="text-center">Saat Ini</th>
                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>

                        {{-- Waktu Melayani Child Header --}}
                        <th class="text-center">Saat Ini</th>
                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="10" class="text-center">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection