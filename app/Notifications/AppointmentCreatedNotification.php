<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use App\Appointment;
use App\Mail\CS\AppointmentCreatedMail;
use App\Notifications\WhatsAppChannel;

class AppointmentCreatedNotification extends Notification implements ShouldQueue
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
        return ['mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(Appointment $appointment): AppointmentCreatedMail
    {
        return (new AppointmentCreatedMail($appointment))->to($appointment->email);
    }

    public function toWhatsApp(Appointment $appointment): string
    {
        $url = url("customer/{$appointment->branch_id}/appointment/booking-status/{$appointment->id}");

        return "KYOO - Hai {$appointment->name}, nomor antrian Anda: {$appointment->number}. Cek antrian di: {$url}";
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
