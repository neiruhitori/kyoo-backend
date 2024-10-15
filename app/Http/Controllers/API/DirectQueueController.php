<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\DirectQueue;
use App\Branch;
use App\Service;
use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Http\Requests\API\DirectQueue\Feedback as DirectQueueFeedback;
use App\Http\Resources\DirectQueue\Detail as DirectQueueDetail;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Events\OnsiteQueueUpdated;
use App\Interfaces\DirectQueueRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DirectQueueController extends Controller
{
    private DirectQueueRepositoryInterface $onsiteRepository;

    public function __construct(DirectQueueRepositoryInterface $onsiteRepository)
    {
        $this->onsiteRepository = $onsiteRepository;
    }

    public function index(Branch $branch)
    {
        $services = Service::where('branch_id', $branch->id)
            ->get();

        foreach ($services as $service) {
            $service->total_queue = DirectQueue::where('service_id', $service->id)
                ->whereDate('created_at', date('Y-m-d'))
                ->where('status', 'waiting')
                ->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'get direct queue services by branch id',
            'data' => $services
        ]);
    }

    public function show(DirectQueue $directQueue)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail direct queues',
            'data' => new DirectQueueDetail($directQueue)
        ]);
    }

    public function store(DirectQueueStore $request)
    {
        try {
            $service = Service::find($request->service_id);

            $data = $request->all();
            $data['client_id'] = $request->cookie('client_id');
            $data['direct_queue_channel'] = 'Mobile Apps';

            $directQueue = $this->onsiteRepository->store($data);

            // send event to update Direct Queue Monitor
            event(new VCTDirectQueueEvent($directQueue, $service->branch_id));
            event(new DirectQueueEvent($directQueue, $service->branch_id));

            if ($directQueue->client_id) {
                event(new OnsiteQueueUpdated($directQueue));
            }

            $branch = Branch::where('id',$directQueue->branch_id)->first();

            $webhookUser = [
                'name' => $directQueue->name,
                'phone' => $directQueue->phone,
                'email' => $directQueue->email,
                'address' => null,
                'emergency_contact' => null,
                'reason_for_visit' => null,
                'date_of_birth' => null,
            ];

            $webhookQueue = [
                'id' => $directQueue->id,
                'service_id' => $directQueue->service_id,
                'service_name' => $directQueue->service_name,
                'service_type' => 'Direct Queue',
                'start_time' => null,
                'end_time' => null,
                'booking_code' => $directQueue->booking_code,
                'branch_name' => $branch->name,
            ];

            return response()->json([
                'success' => true,
                'message' => 'direct queue created',
                'data' => $directQueue,
                'patient' => $webhookUser,
                'appointment' => $webhookQueue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function upcoming()
    {
        $directQueues = DirectQueue::whereUserId(Auth::id())
            ->whereIn('status', ['waiting', 'served', 'requeue'])
            ->whereDate('created_at', '>=', date('Y-m-d'))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'get upcoming direct queues',
            'data' => DirectQueueDetail::collection($directQueues)
        ]);
    }

    public function feedback(DirectQueue $directQueue, DirectQueueFeedback $request)
    {
        $directQueue->update([
            'rating' => $request->rating,
            'is_liked' => $request->is_liked,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'success give feedback direct queue',
            'data' => $directQueue
        ]);
    }

    protected function sendWebhook($client, $webhookUser, $webhookQueue)
    {
     
        $guzzle = new \GuzzleHttp\Client();  

        try {
            $response = $guzzle->post($client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $client->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'patient' => $webhookUser,
                    'appointment' => $webhookQueue,
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Webhook failed with status: ' . $response->getStatusCode());
            }

            return response()->json([
                'status' => 'success',
                'patient' => $webhookUser,
                'appointment' => $webhookQueue,
                'x-secret-token' => $client->secret_token,
                //jgn lupa dihapus pas selesai ygy
               ]);

        } catch (\Exception $e) {
           return response()->json([
            'status' => 'error',
            'message' =>  $e->getMessage()
           ]);
        }
    }

}
