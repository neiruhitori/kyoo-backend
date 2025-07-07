<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\SecretKeyAPi;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $webhookData;

    public function __construct($client, $webhookData)
    {
        $this->client = $client;
        $this->webhookData = $webhookData;
    }

    public function handle():void
    {
       try {
        
            $clientHttp = new Client();
            $tokenAPI = SecretKeyAPi::where('branch_id', $this->client->branch_id)->first();
            $response = $clientHttp->post($this->client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $tokenAPI->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $this->webhookData,
                'timeout' => 5,
            ]);

        } catch (\Exception $e) {
            Log::error("Webhook failed", [
                'error' => $e->getMessage(),
                'url' => $this->client->webhook_url
            ]);
        }
    }
}
