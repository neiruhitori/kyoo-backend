@component('mail::message')
# Appointment

{{ __('Thank you for create appointment using KYOO. Click button below to track your appointment status') }}.

@component('mail::button', ['url' => url('kyooTicket/appointment/' . $branch_id. '/booking-status/' . $appointment_id)])
{{ __('Check My Appointment') }}
@endcomponent

{{ __('Thank you') }},<br>
{{ config('app.name') }}
@endcomponent
