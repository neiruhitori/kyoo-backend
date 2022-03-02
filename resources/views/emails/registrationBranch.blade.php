@component('mail::message')
# {{ __('Verify Your Email') }}

{{ __('Hi :Branch, thank you for register your branch to Kyoo. Please click button below to verify your branch', ['branch' => $branch->name]) }}.

@component('mail::button', ['url' => route('registrationBranch.edit', $branch->id)])
{{ __('Verify Here') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
