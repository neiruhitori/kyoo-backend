@component('mail::message')
# {{ __('Branch Registration Status Update') }}

{{ __('branch.rejected', ['branch' => $branch->name])}}.

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent