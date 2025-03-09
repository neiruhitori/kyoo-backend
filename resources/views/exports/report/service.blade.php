<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Service Report') }}</title>

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
            <strong>{{ __('Date') }}:</strong> {{ $reportTime }}
        </div>
        <div>
            <strong>{{ __('Department') }}:</strong> {{ $department->name }}
        </div>
    </div>

    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle">{{ __('Service') }}</th>
                <th colspan="3" class="text-center">{{ __('Total Queue') }}</th>
                <th colspan="3" class="text-center">{{ __('Waiting Time') }}</th>
                <th colspan="3" class="text-center">{{ __('Served Time') }}</th> 
            </tr>

            <tr>
                <th class="text-right">{{ __('Total Tickets') }}</th>
                <th class="text-right">{{ __('Serve') }}</th>
                <th class="text-right">{{ __('No Show') }}</th>

                <th class="text-center">{{ __('Fastest') }}</th>
                <th class="text-center">{{ __('Average') }}</th>
                <th class="text-center">{{ __('Longest') }}</th>

                <th class="text-center">{{ __('Fastest') }}</th>
                <th class="text-center">{{ __('Average') }}</th>
                <th class="text-center">{{ __('Longest') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $dt)
            <tr>
                <td>{{ $dt['name'] }}</td>
                <td class="text-right">{{ $dt['total_queue'] }}</td>
                <td class="text-right">{{ $dt['total_served'] }}</td>
                <td class="text-right">{{ $dt['total_no_show'] }}</td>
                <td class="text-center">{{ $dt['shortest_wait_duration'] }}</td>
                <td class="text-center">{{ $dt['average_wait_duration'] }}</td>
                <td class="text-center">{{ $dt['longest_wait_duration'] }}</td>
                <td class="text-center">{{ $dt['shortest_serve_duration'] }}</td>
                <td class="text-center">{{ $dt['average_serve_duration'] }}</td>
                <td class="text-center">{{ $dt['longest_serve_duration'] }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('Data not Found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>