@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3>Laporan Cabang</h3>
</div>

<form class="mb-4">
    <div class="form-row align-items-end">
        <div class="col-auto">
            <label for="deparmentId">Cabang</label>
            <select
                name="branch_id"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                <option value selected>Semua Cabang</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branch->id != $branch_id ?: 'selected' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-auto">
            <label for="month">Bulan</label>
            <select
                id="month"
                name="month"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month != $i ?: 'selected' }}>
                        {{ date('F', strtotime('2020-' . $i . '-01')) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-auto">
            <label for="year">Tahun</label>
            <select
                id="year"
                name="year"
                class="form-control"
                style="width: 180px;"
                autocomplete="off"
            >
                @for ($i = 2000; $i <= date('Y'); $i++)
                    <option value="{{ $i }}" {{ $year != $i ?: 'selected' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Cabang</th>
                        <th rowspan="2" class="align-middle text-right">Dilayani</th>
                        <th rowspan="2" class="align-middle text-right">Tidak Hadir</th>
                        <th colspan="2" class="text-center">Waktu Tunggu</th>
                        <th colspan="2" class="text-center">Waktu Melayani</th>
                    </tr>

                    <tr>
                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>

                        <th class="text-center">Rata-Rata</th>
                        <th class="text-center">Terlama</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->branch->name }}</td>
                            <td class="text-right">{{ $report->total_served }}</td>
                            <td class="text-right">{{ $report->total_no_show }}</td>
                            <td class="text-center">{{ $report->average_wait_duration }}</td>
                            <td class="text-center">{{ $report->longest_wait_duration }}</td>
                            <td class="text-center">{{ $report->average_serve_duration }}</td>
                            <td class="text-center">{{ $report->longest_serve_duration }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection