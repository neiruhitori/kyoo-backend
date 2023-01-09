@component('mail::message')
# Appointment Dibatalkan

Appointment Anda pada tanggal {{ date('Y-m-d', strtotime($appointment->date)) }} di {{ $appointment->Branch->name }} dibatalkan. Klik link dibawah untuk melihat status antrian Anda.

@component('mail::button', ['url' => $appointmentStatusURL])
Cek Appointment Saya
@endcomponent

Terima kasih,<br>
{{ $appointment->Branch->name }}
@endcomponent