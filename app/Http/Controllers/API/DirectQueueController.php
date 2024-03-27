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

            return response()->json([
                'success' => true,
                'message' => 'direct queue created',
                'data' => $directQueue
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
}
