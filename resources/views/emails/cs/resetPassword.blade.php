@component('mail::message')
# {{ __('Resetting VCT Password') }}

{{ __('password.request', ['user' => $user->name]) }}.

@component('mail::button', ['url' => route('adminBranch.user.reset', $user_id)])
{{ __('Reset Password') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
