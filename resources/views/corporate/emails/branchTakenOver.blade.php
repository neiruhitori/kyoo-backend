@component('mail::message')
Hai, {{ $user->name }}.

{{ $branch->name }} telah ditambahkan sebagai cabang <strong>{{ $corporate->name }}</strong>.<br>
Jika Anda merasa ini adalah kesalahan, silahkan hubungi [{{ $adminKyoo }}](mailto:{{ $adminKyoo }})

Terima kasih,<br>
Admin Kyoo
@endcomponent