<?php

namespace App\Notifications;

use App\Mail\CS\AppointmentOnsiteCreatedMail;
use App\Models\AppointmentOnsite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AppointmentOnsiteCreatedNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(AppointmentOnsite $appointmentOnsite): AppointmentOnsiteCreatedMail
    {
        return (new AppointmentOnsiteCreatedMail($appointmentOnsite))->to($appointmentOnsite->email);
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
