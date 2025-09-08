@component('mail::message')

{{ __('To complete verification, please use the code')}}:

<div style="display:flex; justify-content: center;">
    <div style="background-color: rgb(125, 125, 125);">
        <h3 style="font-weight: bolder; color: white; padding: 0rem 1rem;">{{ $code }}</h3>
    </div>
</div>

<hr>
<small>
    {{ __('The code can be used only once and will expire in 30 minutes. If you didn\'t request the code, please disregard this email. This is an generated email, please do not reply') }}
</small>
<hr>

<br>{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
