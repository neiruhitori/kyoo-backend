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
            <td colspan="16" style="text-align: center;">
                <strong style="text-transform: uppercase;">{{ $branch->name }}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="16" style="text-align: center">
                {{ __('Waiting Service Distribution Report') }}
            </td>
        </tr>

        <tr></tr>
        <tr>
            <td><strong>{{ __('Date') }}:</strong></td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('Department') }}:</strong></td>
            <td>{{ $department->name }}</td>
        </tr>
        <tr></tr>

        <thead>
            <tr>
                <th rowspan="2" style="width: 120px;">{{ __('Service') }}</th>
                <th colspan="14" style="text-align: center;">{{ __('Tickets in Interval (Minutes)') }}</th>
                <th rowspan="2" style="text-align: right;">Total</th>
            </tr>

            <tr>
                <th style="text-align: center;" colspan="2">0-5</th>
                <th style="text-align: center;" colspan="2">5-10</th>
                <th style="text-align: center;" colspan="2">10-15</th>
                <th style="text-align: center;" colspan="2">15-20</th>
                <th style="text-align: center;" colspan="2">20-25</th>
                <th style="text-align: center;" colspan="2">25-30</th>
                <th style="text-align: center;" colspan="2">>=30</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $dt)
            <tr>
                <td>{{ $dt->name }}</td>
                <td style="text-align: center;">{{ $dt->_0_5 }}</td>
                <td style="text-align: right;">{{ $dt->_0_5_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_5_10 }}</td>
                <td style="text-align: right;">{{ $dt->_5_10_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_10_15 }}</td>
                <td style="text-align: right;">{{ $dt->_10_15_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_15_20 }}</td>
                <td style="text-align: right;">{{ $dt->_15_20_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_20_25 }}</td>
                <td style="text-align: right;">{{ $dt->_20_25_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_25_30 }}</td>
                <td style="text-align: right;">{{ $dt->_25_30_percentage }}</td>
                <td style="text-align: center;">{{ $dt->_30_ }}</td>
                <td style="text-align: right;">{{ $dt->_30__percentage }}</td>
                <td style="text-align: right;">{{ $dt->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>