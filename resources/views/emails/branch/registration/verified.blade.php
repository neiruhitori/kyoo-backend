@component('mail::message')
# Your Branch Has Been Verified

Hi {{$branch->name}}, thank you for register your branch to Kyoo.id. Now your branch has been verified.

Thank You,<br>
{{ config('app.name') }}
@endcomponent