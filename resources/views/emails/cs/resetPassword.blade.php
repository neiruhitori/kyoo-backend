@component('mail::message')
# Resetting VCT Password!

You are receiving this email because we received a password reset request for <b>{{$user->name}}'s</b> account.

@component('mail::button', ['url' => route('adminBranch.user.reset', $user_id)])
Reset Password
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
