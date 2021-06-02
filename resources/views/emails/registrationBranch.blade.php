@component('mail::message')
# Verify Your Email

Hi {{$branch->name}}, thank you for register your branch to Kyoo. Please click button below to verify your branch.

@component('mail::button', ['url' => route('registrationBranch.edit', $branch->id)])
Verify Here
@endcomponent

Thank You,<br>
{{ config('app.name') }}
@endcomponent
