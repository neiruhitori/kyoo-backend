@component('mail::message')
Click button below to give us Feedback

@component('mail::button', ['url' => url($survey_link)])
{{ __('Give Feedback') }}
@endcomponent

Thank you,<br>
{{ $branch_name }}
@endcomponent
