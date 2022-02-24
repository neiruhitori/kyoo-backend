@component('mail::message')
# {{ __('User Register') }}

{{ __('user.confirmation', ['user' => $user->name]) }}.

@component('mail::button', ['url' => route('user.userRegister', $id)])
{{ __('Verify Here') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
