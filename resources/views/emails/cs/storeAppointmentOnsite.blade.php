@component('mail::message')
# Appointment Onsite

{{ __('You have made an appointment on :booking_date at :branch_name. Click the button below to track your appointment.',['booking_date' => $booking_date,'branch_name' => $branch_name ]) }}
{{-- Anda telah membuat appointment onsite pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Appointment Anda. --}}

<div style="text-align: center;">
<table style="margin-left: auto; margin-right: auto;">
<tr>
<td style="text-align: left; padding-right: 5px;">{{ __('Kode Booking') }}</td>
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
<img src="{{ asset('storage/' . $qr_code) }}" alt="{{ strtoupper($booking_code) }}">
</div>

@component('mail::button', ['url' => $url])
{{ __('Check My Appointment Onsite') }}
@endcomponent

Terima kasih,<br>
{{ $branch_name }}
@endcomponent
