@component('mail::message')
# {{ __('Verify New Email') }}

{{ __('email.confirmation', ['user' => $changeEmail->User->name]) }}.

@component('mail::button', ['url' => route('user.changeEmail', $id)])
{{ __('Verify Here') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
