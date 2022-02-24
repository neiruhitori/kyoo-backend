@component('mail::message')
# {{ __('Reset Password Notification') }}

{{ __('You are receiving this email because we received a password reset request for your account') }}.<br>
{{ __('This password reset link will expire in :count minutes', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}.<br>

@component('mail::button', ['url' => $url])
{{ __('Reset Password') }}
@endcomponent

{{ __('If you did not request a password reset, no further action is required') }}.<br>

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent