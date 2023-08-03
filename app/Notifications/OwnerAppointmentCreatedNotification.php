<?php

namespace App\Notifications;

use App\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OwnerAppointmentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [OwnerWhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toWhatsApp(Appointment $appointment): string
    {
        $url = url("customer/{$appointment->branch_id}/appointment/booking-status/{$appointment->id}");

        return "KYOO - Anda menerima Order Reservasi baru untuk {$appointment->Branch->name} dengan waktu reservasi di {$appointment->Slot->start_time} - {$appointment->Slot->end_time} tanggal {$appointment->date} atas nama {$appointment->name} dengan nomor antrian {$appointment->number}. Lihat detail reservasi di https://kyoo.id";
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
