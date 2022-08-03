<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $date }}</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="15" style="text-align: center;">
                <strong style="text-transform: uppercase;">{{ $branch->name }}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="15" style="text-align: center">
                {{ $title }}
            </td>
        </tr>

        <tr></tr>
        <tr>
            <td><strong>Tanggal:</strong></td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            <td><strong>Departemen:</strong></td>
            <td>{{ $department->name }}</td>
        </tr>
        <tr></tr>

        <thead>
            <tr>
                <th rowspan="2" style="width: 120px; font-weight: bold;">User</th>
                <th rowspan="2" style="width: 120px; font-weight: bold;">Layanan</th>
                <th colspan="3" style="font-weight: bold; text-align: center">Total Antrian</th>
                <th rowspan="2" style="width: 120px; font-weight: bold; text-align: center">Waktu Operasional</th>
                <th rowspan="2" style="width: 120px; font-weight: bold; text-align: center">Total Waktu Layanan</th>
                <th rowspan="2" style="width: 120px; font-weight: bold; text-align: center">Total Waktu Idle</th>
                <th rowspan="2" style="width: 120px; font-weight: bold; text-align: right">Produktivitas</th>
                <th colspan="3" style="font-weight: bold; text-align: center">Waktu Tunggu</th>
                <th colspan="3" style="font-weight: bold; text-align: center">Waktu Dilayani</th> 
            </tr>

            <tr>
                <th style="width: 90px; font-weight: bold; text-align: right">Jumlah Tiket</th>
                <th style="width: 90px; font-weight: bold; text-align: right">Dilayani</th>
                <th style="width: 90px; font-weight: bold; text-align: right">Tidak Hadir</th>

                <th style="width: 90px; font-weight: bold; text-align: center">Tercepat</th>
                <th style="width: 90px; font-weight: bold; text-align: center">Rata-Rata</th>
                <th style="width: 90px; font-weight: bold; text-align: center">Terlama</th>

                <th style="width: 90px; font-weight: bold; text-align: center">Tercepat</th>
                <th style="width: 90px; font-weight: bold; text-align: center">Rata-Rata</th>
                <th style="width: 90px; font-weight: bold; text-align: center">Terlama</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $dt)
            <tr>
                <td>{{ $dt->name }}</td>
                <td>{{ $dt->services }}</td>
                <td style="text-align: right">{{ $dt->total_queue }}</td>
                <td style="text-align: right">{{ $dt->total_served }}</td>
                <td style="text-align: right">{{ $dt->total_no_show }}</td>
                <td style="text-align: center">{{ $dt->total_operating_duration }}</td>
                <td style="text-align: center">{{ $dt->total_serve_duration }}</td>
                <td style="text-align: center">{{ $dt->total_idle_duration }}</td>
                <td style="text-align: right">{{ $dt->productivity_percentage }}</td>
                <td style="text-align: center">{{ $dt->shortest_wait_duration }}</td>
                <td style="text-align: center">{{ $dt->average_wait_duration }}</td>
                <td style="text-align: center">{{ $dt->longest_wait_duration }}</td>
                <td style="text-align: center">{{ $dt->shortest_serve_duration }}</td>
                <td style="text-align: center">{{ $dt->average_serve_duration }}</td>
                <td style="text-align: center">{{ $dt->longest_serve_duration }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>