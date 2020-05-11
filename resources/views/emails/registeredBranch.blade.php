@component('mail::message')
# Verify Your Email

Hi {{$branch->name}}, thank you for register your branch to Kyoo.id. Please login on our web and <b>CHANGE</b> your default password.

<center>
    Your password is
    <br>
    <h5>{{ $password }}</h5>
</center>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
