@component('mail::message')
# Appointment Onsite

Anda telah membuat appointment onsite pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Appointment Anda.

@component('mail::button', ['url' => url('customer/' . $branch_id. '/appointment-onsite/booking-status/' . $appointment_onsite_id)])
{{ __('Check My Appointment Onsite') }}
@endcomponent

Terima kasih,<br>
{{ $branch_name }}
@endcomponent
