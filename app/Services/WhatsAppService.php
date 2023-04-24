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

    public function authenticate()
    {
        return Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->post("{$this->waUrl}/authentication")
            ->throw()
            ->collect();
    }

    public function me()
    {
        return Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->get("{$this->waUrl}/me")
            ->throw()
            ->collect();
    }

    public function sendTextMessage(string $phoneNumber, string $message)
    {
        Http::withHeaders([
            'Accept' => 'application/json'
        ])
            ->post("{$this->waUrl}/messages", [
                'phone_number' => $phoneNumber,
                'message' => $message
            ])
            ->throw()
            ->collect();

        return;
    }
}
