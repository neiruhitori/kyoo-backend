<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Services\WhatsAppService;

class WhatsAppChannel
{
    private WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $this->whatsAppService->sendTextMessage(
            $notifiable->phone,
            $notification->toWhatsApp($notifiable)
        );
    }
}
