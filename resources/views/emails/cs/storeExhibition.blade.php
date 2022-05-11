@component('mail::message')
# {{ __('Exhibition') }}

Anda telah memesan antrian pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Antrian Anda.

@component('mail::button', ['url' => url('customer/' . $branch_id . '/exhibition/booking-status/' . $exhibition_id)])
{{ __('Check My Queue') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ $branch_name }}
@endcomponent
