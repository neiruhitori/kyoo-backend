@component('mail::message')
# Akun Corporate Anda Telah Terdaftar di Kyoo

Hai {{ $corporate->name }}, terima kasih telah mendaftarkan corporate Anda di Kyoo. Silahkan reset password Anda untuk login ke web Kyoo menggunakan link dibawah

@component('mail::button', ['url' => $resetPasswordUrl])
Reset Password
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent