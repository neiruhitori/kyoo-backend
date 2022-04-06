@component('mail::message')
# Appointment

Anda telah membuat appointment pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Appointment Anda.

@component('mail::button', ['url' => url('kyooTicket/appointment/' . $branch_id. '/booking-status/' . $appointment_id)])
{{ __('Check My Appointment') }}
@endcomponent

Terima kasih ,<br>
{{ $branch_name }}
@endcomponent
