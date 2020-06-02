@component('mail::message')
# Verify New Email

Hi {{ $changeEmail->User->name }}, please click button below to verify your new email.

@component('mail::button', ['url' => route('user.changeEmail', $id)])
Verify
@endcomponent

Thank You,<br>
{{ config('app.name') }}
@endcomponent
