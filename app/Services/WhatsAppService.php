<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private $waUrl;

    public function __construct()
    {
        $this->waUrl = config('app.kyoo_wa_url');
    }

    public function sendTextMessage(string $phoneNumber, string $message)
    {
        Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->post("{$this->waUrl}/message", [
                'phone_number' => $phoneNumber,
                'message' => $message
            ])
            ->throw()
            ->collect();

        return;
    }
}
