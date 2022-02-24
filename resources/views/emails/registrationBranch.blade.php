@component('mail::message')
# {{ __('Verify Your Email') }}

{{ __('branch.confirmation', ['branch' => $branch->name]) }}.

@component('mail::button', ['url' => route('registrationBranch.edit', $branch->id)])
{{ __('Verify Here') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
