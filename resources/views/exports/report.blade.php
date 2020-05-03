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
            <th>Transaction ID</th>
            <th>Customer ID</th>
            <th>VCT ID</th>
            <th>Transaction Date</th>
            <th>Branch ID</th>
            <th>Service ID</th>
            <th>Appointment Booking Code</th>
            <th>Appointment Date</th>
            <th>Appointment Channel</th>
            <th>Appointment Status</th>
            <th>Datetime Checkin</th>
            <th>Datetime Served</th>
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