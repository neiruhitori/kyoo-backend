<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Document') }}</title>
</head>
<body>
    <table>
        <tr>
            <th><b>{{ __('Transaction ID') }}</b></th>
            <th><b>{{ __('Customer ID') }}</b></th>
            <th><b>{{ __('VCT ID') }}</b></th>
            <th><b>{{ __('Transaction Date') }}</b></th>
            <th><b>{{ __('Branch ID') }}</b></th>
            <th><b>{{ __('Service ID') }}</b></th>
            <th><b>{{ __('Appointment Booking Code') }}</b></th>
            <th><b>{{ __('Appointment Date') }}</b></th>
            <th><b>{{ __('Channel') }}</b></th>
            <th><b>{{ __('Status') }}</b></th>
            <th><b>{{ __('End Served Time') }}</b></th>
            <th><b>{{ __('Queue Number') }}</b></th>
        </tr>
        @forelse ($queue as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->user_id }}</td>
                <td>{{ $item->vct_id }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->Slot->Service->branch_id }}</td>
                <td>{{ $item->Slot->Service->name }}</td>
                <td>{{ $item->booking_code }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->channel }}</td>
                <td>{{ __(ucwords($item->status)) }}</td>
                <td>{{ $item->end_served_time }}</td>
                <td>{{ $item->queue_order }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12">{{ __('No Data') }}</td>
            </tr>
        @endforelse
    </table>
</body>
</html>