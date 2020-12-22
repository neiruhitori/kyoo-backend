@component('mail::message')
# Branch Registration Status Update

Hi {{$branch->name}}, thank you for register your branch to Kyoo.id. Your branch are rejected, please contact admin for futher information.

Thank You,<br>
{{ config('app.name') }}
@endcomponent