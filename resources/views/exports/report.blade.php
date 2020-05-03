<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th><b>Transaction ID</b></th>
            <th><b>Customer ID</b></th>
            <th><b>VCT ID</b></th>
            <th><b>Transaction Date</b></th>
            <th><b>Branch ID</b></th>
            <th><b>Service ID</b></th>
            <th><b>Appointment Booking Code</b></th>
            <th><b>Appointment Date</b></th>
            <th><b>Appointment Channel</b></th>
            <th><b>Appointment Status</b></th>
            <th><b>Datetime Checkin</b></th>
            <th><b>Datetime Served</b></th>
        </tr>
        @forelse ($appointments as $appointment)
            <tr>
                <td>{{ $appointment->id }}</td>
                <td>{{ $appointment->user_id }}</td>
                <td>{{ $appointment->vct_id }}</td>
                <td>{{ $appointment->created_at }}</td>
                <td>{{ $appointment->Slot->Service->branch_id }}</td>
                <td>{{ $appointment->Slot->service_id }}</td>
                <td>{{ $appointment->booking_code }}</td>
                <td>{{ $appointment->date }}</td>
                <td>{{ $appointment->Slot->Service->name }}</td>
                <td>{{ $appointment->status }}</td>
                <td>{{ $appointment->checkin_time }}</td>
                <td>{{ $appointment->served_time }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12">No Data</td>
            </tr>
        @endforelse
    </table>
</body>
</html>