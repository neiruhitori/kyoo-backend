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
        if (
            $notifiable->phone &&
            $notifiable->Branch &&
            $notifiable->Branch->is_premium &&
            $notifiable->Branch->BranchConfiguration->wa_notification != false
        ) {
            $this->whatsAppService->sendTextMessage(
                $notifiable->phone,
                $notification->toWhatsApp($notifiable)
            );
        }
    }
}
