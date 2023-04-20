<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use App\DirectQueue;
use App\Notifications\WhatsAppChannel;

class OnsiteQueueCreatedNotification extends Notification implements ShouldQueue
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
    public function via()
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(DirectQueue $directQueue): string
    {
        $url = url("customer/{$directQueue->branch_id}/onsite/booking-status/{$directQueue->id}");

        return "KYOO - Hai {$directQueue->name}, nomor antrian Anda: {$directQueue->queue_no}. Cek antrian di: {$url}";
    }
}
