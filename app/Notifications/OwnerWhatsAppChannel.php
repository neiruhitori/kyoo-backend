<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Services\WhatsAppService;

class OwnerWhatsAppChannel extends Notification
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
      $notifiable->Branch->BranchConfiguration->wa_notification_owner != false
    ) {
      $this->whatsAppService->sendTextMessage(
        $notifiable->Branch->BranchConfiguration->phone_owner,
        $notification->toWhatsApp($notifiable)
      );
    }
  }
}
