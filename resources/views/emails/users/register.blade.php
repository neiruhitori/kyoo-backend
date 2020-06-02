@component('mail::message')
# User Register

Hi {{ $user->name }}, thanks for register on {{ config('app.name') }}, please click button below to verify your account.

@component('mail::button', ['url' => route('user.userRegister', $id)])
Verify
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
