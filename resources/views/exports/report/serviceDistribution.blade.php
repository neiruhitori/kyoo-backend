<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Distribusi Tunggu Layanan</title>

    <style>
        html, body {
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
            color: #495057;
        }

        .table {
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            background-color: #e9ecef;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }

        .table td, .table th {
            border-top: 1px solid #dee2e6;
            padding: .5rem;
        }

        td.text-center, th.text-center {
            text-align: center;
        }

        td.text-right, th.text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div style="margin-bottom: 1.25rem;">
        <h2 class="report-title">{{ $title }}</h2>
        <div style="margin-bottom: .25rem">{{ $branch['name'] }}</div>
        <div style="margin-bottom: .25rem">
            <strong>Tanggal:</strong> {{ $reportTime }}
        </div>
        <div>
            <strong>Departemen:</strong> {{ $department->name }}
        </div>
    </div>

    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle">Layanan</th>
                <th colspan="14" class="text-center">Jumlah Tiket dalam Interval (Menit)</th>
                <th rowspan="2" class="align-middle text-right">Total</th>
            </tr>

            <tr>
                <th class="text-center" colspan="2">0-5</th>
                <th class="text-center" colspan="2">5-10</th>
                <th class="text-center" colspan="2">10-15</th>
                <th class="text-center" colspan="2">15-20</th>
                <th class="text-center" colspan="2">20-25</th>
                <th class="text-center" colspan="2">25-30</th>
                <th class="text-center" colspan="2">>=30</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $dt)
            <tr>
                <td>{{ $dt->name }}</td>
                <td class="text-center">{{ $dt->_0_5 }}</td>
                <td class="text-right">{{ $dt->_0_5_percentage }}</td>
                <td class="text-center">{{ $dt->_5_10 }}</td>
                <td class="text-right">{{ $dt->_5_10_percentage }}</td>
                <td class="text-center">{{ $dt->_10_15 }}</td>
                <td class="text-right">{{ $dt->_10_15_percentage }}</td>
                <td class="text-center">{{ $dt->_15_20 }}</td>
                <td class="text-right">{{ $dt->_15_20_percentage }}</td>
                <td class="text-center">{{ $dt->_20_25 }}</td>
                <td class="text-right">{{ $dt->_20_25_percentage }}</td>
                <td class="text-center">{{ $dt->_25_30 }}</td>
                <td class="text-right">{{ $dt->_25_30_percentage }}</td>
                <td class="text-center">{{ $dt->_30_ }}</td>
                <td class="text-right">{{ $dt->_30__percentage }}</td>
                <td class="text-right">{{ $dt->total }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>