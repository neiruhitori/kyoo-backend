<?php

namespace App\Http\Controllers\AdminBranch;

use Carbon\Carbon;
use App\Jobs\SendWebhook;
use App\Models\WebhookLogs;
use App\BranchConfiguration;
use App\Models\SecretKeyAPi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    protected $sandboxData;

    public function __construct()
    {
        $this->sandboxData = [
            'event_type' => 'onsite_checkin_booking',
            'queue' => [
                'id' => 12345687890,
                'service_id' => 54321,
                'branch_id' => 620,
                'booking_code' => "XYZ123",
                'service_type' => 'Appointment Onsite Queue',
                'check_in_status' => true,
                'check_in_date' => now()->toISOString(),
                'created_at' => now()->toISOString(),
            ],
            'branch' => [
                'id' => 620,
                'name' => "KYOO BRANCH NAME",
            ],
            'service' => [
                'id' => 54321,
                'name' => "KYOO SERVICE NAME",
                'branch_id' => 620,
                'branch_name' => "KYOO BRANCH NAME",
            ],
        ];
    }
    
    public function index()
    {
        $endpoint = BranchConfiguration::where('branch_id', Auth::user()->Branch->id)
                                        ->select(['webhook_url','sandbox_url'])
                                        ->first();

        // dd($logs);
        return view('adminBranch.webhook.index',[
                                                    'webhook' => $endpoint->webhook_url,
                                                    'sandbox' => $endpoint->sandbox_url,
                                                    'sandbox_data' => $this->sandboxData,
                                                ]);
    }

    public function fetchWebhook()
    {
        $logs = WebhookLogs::with('queue:id,name')
                            ->where('branch_id', Auth::user()->Branch->id)
                            ->latest()
                            ->take(10)
                            ->get()
                            ->map(function ($log) {
                            $log->created_at_formatted = $log->created_at->format('d-m-Y H:i');
                            return $log;
                        });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function sendDummy()
    {
        $client = BranchConfiguration::where('branch_id', Auth::user()->Branch->id)->first();
        $tokenAPI = SecretKeyAPi::where('branch_id', Auth::user()->Branch->id)->first();

        $response = Http::timeout(5)->withHeaders([
            'x-secret-token' => $tokenAPI->secret_token,
            'Content-Type'   => 'application/json',
        ])->post($client->sandbox_url, $this->sandboxData);

        return response()->json([
            'status' => $response->successful() ? 'OK' : 'ERR',
            'code'   => $response->status(),
            'body'   => $response->json(),
        ]);
    }

    public function resend($id,$mode)
    {
        $log = WebhookLogs::where('branch_id', Auth::user()->Branch->id)
                            ->where('id', $id)
                            ->first();
        $client = BranchConfiguration::where('branch_id', Auth::user()->Branch->id)->first();

        if($log && $client){
            SendWebhook::dispatch($client, $log->payload, $mode);
            $log->touch();
            return redirect()->back()->with('success', 'Webhook resend');
        }
        return redirect()->back()->with('error', 'Log or Client not found');
    }
}
