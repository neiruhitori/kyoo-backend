<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\WebhookLogs;
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
        if (is_string($webhookData)) {
            $webhookData = json_decode($webhookData, true);
        }
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

            if ($this->client->is_live_test) {
                $status = $response->getStatusCode();
                $log = WebhookLogs::UpdateOrCreate(
                        [
                            'branch_id'  => $this->client->branch_id,
                            'queue_id'  => $this->webhookData['queue']['id'],
                        ],
                        [
                            'event_type'  => $this->webhookData['event_type'],
                            'payload' => json_encode($this->webhookData),
                            'status_code' => $status ?? null,
                        ]
                    );
            }


        } catch (\Exception $e) {
            Log::error("Webhook failed", [
                'error' => $e->getMessage(),
                'url' => $this->client->webhook_url
            ]);
            if ($this->client->is_live_test) {
                $log = WebhookLogs::UpdateOrCreate(
                        [
                            'branch_id'  => $this->client->branch_id,
                            'queue_id'  => $this->webhookData['queue']['id'],
                        ],
                        [
                            'event_type'  => $this->webhookData['event_type'],
                            'payload' => json_encode($this->webhookData),
                            'status_code' => 0,
                        ]
                    );
            }

        }
    }
}
