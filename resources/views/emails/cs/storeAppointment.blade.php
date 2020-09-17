@component('mail::message')
# Appointment

Thank you for create appointment using KYOO. Click button below to track your appointment status.

@component('mail::button', ['url' => route('appointment.status', $appointment_id)])
Check My Appointment
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
