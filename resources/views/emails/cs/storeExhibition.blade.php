@component('mail::message')
# {{ __('Exhibition') }}

{{ __('Thank you for queueing using KYOO. Click button below to track your queue status') }}.

@component('mail::button', ['url' => route('exhibition.status', $exhibition_id)])
{{ __('Check My Queue') }}
@endcomponent

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
