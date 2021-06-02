@component('mail::message')
# Your Branch Has Been Verified

Hi {{$branch->name}}, thank you for register your branch to Kyoo.id. Now your branch has been verified.
<br>
Let's login to KYOO and update branch profile and virtual counter user to start the Queue
@component('mail::button', ['url' => route('login')])
Login
@endcomponent

Thank You,<br>
{{ config('app.name') }}
@endcomponent