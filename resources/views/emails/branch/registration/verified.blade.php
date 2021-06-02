@component('mail::message')
# Your Branch Has Been Verified

Hi {{$branch->name}}, thank you for register your branch to Kyoo.id. Now your branch has been verified.
<br>
<br>
<p>Let's login to KYOO and update branch profile and virtual counter user to start the Queue</p>
<a href="{{route('login')}}" class="btn btn-sm btn-primary">Login</a>

Thank You,<br>
{{ config('app.name') }}
@endcomponent