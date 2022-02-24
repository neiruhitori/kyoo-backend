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
            <th><b>{{ __('Check-In Time') }}</b></th>
            <th><b>{{ __('Served Time') }}</b></th>
            <th><b>{{ __('End Served Time') }}</b></th>
            <th><b>{{ __('Queue Number') }}</b></th>
        </tr>
        @forelse ($appointments as $appointment)
            <tr>
                <td>{{ $appointment->id }}</td>
                <td>{{ $appointment->user_id }}</td>
                <td>{{ $appointment->vct_id }}</td>
                <td>{{ $appointment->created_at }}</td>
                <td>{{ $appointment->Slot->Service->branch_id }}</td>
                <td>{{ $appointment->Slot->Service->name }}</td>
                <td>{{ $appointment->booking_code }}</td>
                <td>{{ $appointment->date }}</td>
                <td>{{ $appointment->appointment_channel }}</td>
                <td>{{ $appointment->status }}</td>
                <td>{{ $appointment->checkin_time }}</td>
                <td>{{ $appointment->served_time }}</td>
                <td>{{ $appointment->end_served_time }}</td>
                <td>{{ $appointment->number }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12">{{ __('No Data') }}</td>
            </tr>
        @endforelse
    </table>
</body>
</html>