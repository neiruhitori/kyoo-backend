@component('mail::message')
# Appointment Onsite

Anda telah membuat appointment onsite pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Appointment Anda.

<div style="text-align: center;">
<table style="margin-left: auto; margin-right: auto;">
<tr>
<td style="text-align: left; padding-right: 5px;">Kode Booking</td>
<td style="text-align: left;">: {{ strtoupper($booking_code) }}</td>
</tr>
<tr>
<td style="text-align: left; padding-right: 5px;">Jam Booking</td>
<td style="text-align: left;">: {{ $start_time }} - {{ $end_time }}</td>
</tr>
<tr>
<td style="text-align: left; padding-right: 5px;">Tanggal Booking</td>
<td style="text-align: left;">: {{ $booking_day }}, {{ $booking_date }}</td>
</tr>
<tr>
<td style="text-align: left; padding-right: 5px;">Jenis Layanan</td>
<td style="text-align: left;">: {{ $service_name }}</td>
</tr>
<tr>
<td style="text-align: left; padding-right: 5px;">Alamat</td>
<td style="text-align: left;">: {{ $address }}</td>
</tr>
</table>
</div>

<div style="text-align: center;">
<img src="{{ Storage::url($qr_code) }}" alt="{{ strtoupper($booking_code) }}">
</div>

@component('mail::button', ['url' => url('customer/' . $branch_id. '/appointment-onsite/booking-status/' . $appointment_onsite_id)])
{{ __('Check My Appointment Onsite') }}
@endcomponent

Terima kasih,<br>
{{ $branch_name }}
@endcomponent
