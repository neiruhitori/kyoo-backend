@component('mail::message')
Hi,

{{ __('We\'d love to hear from you!') }}
{{ __('Please take a short survey to share your thoughts about your recent visit to our branch so we can keep improving our service.') }}
{{ __('Our short survey only takes a few minutes.') }} 

{{ __('Just click link below to get started.') }}

@component('mail::button', ['url' => url($survey_link)])
{{ __('Give Feedback') }}
@endcomponent
{{ __('Thank You') }},<br>
{{ $branch_name }}
@endcomponent
