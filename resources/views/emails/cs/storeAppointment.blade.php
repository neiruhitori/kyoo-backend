@component('mail::message')
# Appointment

{{-- Anda telah membuat appointment pada tanggal {{ $booking_date }} di {{ $branch_name }}. Klik tombol dibawah untuk melacak Appointment Anda. --}}
{{ __('You have made an appointment on :booking_date at :branch_name. Click the button below to track your appointment.',['booking_date' => $booking_date,'branch_name' => $branch_name ]) }}

@component('mail::button', ['url' => url($url)])
{{ __('Check My Appointment') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ $branch_name }}
@endcomponent
