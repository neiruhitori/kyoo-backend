@component('mail::message')
# {{ __('Verify Your Email') }}

{{ __('branch.registered', ['branch' => $branch->name]) }}.

<center>
    {{ __('Your password is') }}
    <br>
    <h5>{{ $password }}</h5>
</center>

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
