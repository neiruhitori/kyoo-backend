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
    public function index()
    {
        $endpoint = BranchConfiguration::where('branch_id', Auth::user()->Branch->id)
                                        ->select(['webhook_url','sandbox_url'])
                                        ->first();

        $logs = WebhookLogs::with('queue:id,name')
                            ->where('branch_id', Auth::user()->Branch->id)
                            ->latest()
                            ->take(10)
                            ->get()
                            ->map(function ($log) {
                            $log->created_at_formatted = $log->created_at->format('d-m-Y H:i');
                            if (is_string($log->payload)) {
                                $decoded = json_decode($log->payload, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $log->payload = $decoded;
                                }
                            }
                            return $log;
                        });

        // dd($logs);
        return view('adminBranch.webhook.index',[
                                                    'logs' => $logs,
                                                    'webhook' => $endpoint->webhook_url,
                                                    'sandbox' => $endpoint->sandbox_url,
                                                ]);
    }

    public function resend($id)
    {
        $log = WebhookLogs::where('branch_id', Auth::user()->Branch->id)
                            ->where('id', $id)
                            ->first();
        $client = BranchConfiguration::where('branch_id', Auth::user()->Branch->id)->first();

        if($log && $client){
            SendWebhook::dispatch($client, $log->payload);
            $log->touch();
            return redirect()->back()->with('success', 'Webhook resend');
        }
        return redirect()->back()->with('error', 'Log or Client not found');
    }
}
