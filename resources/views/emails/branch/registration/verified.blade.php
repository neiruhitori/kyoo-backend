@component('mail::message')
# {{ __('Your Branch Has Been Verified') }}

{{ __('Hi :Branch, thank you for register your branch to Kyoo.id. Now your branch has been verified', ['branch' => $branch->name]) }}.
<br>
{{ __('Let\'s login to KYOO and update branch profile and virtual counter user to start the Queue')}}
@component('mail::button', ['url' => route('login')])
Login
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent