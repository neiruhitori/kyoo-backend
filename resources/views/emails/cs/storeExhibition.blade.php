@component('mail::message')
# {{ __('Exhibition') }}

Anda telah memesan antrian pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Antrian Anda.

@component('mail::button', ['url' => url('kyooTicket/exhibition/' . $branch_id . '/booking-status/' . $exhibition_id)])
{{ __('Check My Queue') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ $branch_name }}
@endcomponent
