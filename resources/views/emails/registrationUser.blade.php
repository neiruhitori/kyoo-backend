@component('mail::message')
# {{ __('Verify Your Email') }}

{{ __('Hi :User, thank you for register your account to Kyoo. Please click button below to verify your account', ['user' => $user->name]) }}.

@component('mail::button', ['url' => url($verif_link)])
{{ __('Verify Here') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
