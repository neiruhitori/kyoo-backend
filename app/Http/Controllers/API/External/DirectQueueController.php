<?php

namespace App\Http\Controllers\API\External;

use App\Http\Controllers\Controller;
use App\Service;
use App\Http\Requests\API\External\DirectQueue\Store;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Events\OnsiteQueueUpdated;

class DirectQueueController extends Controller
{
    protected $onsiteRepository;

    public function __construct(DirectQueueRepositoryInterface $onsiteRepository)
    {
        $this->onsiteRepository = $onsiteRepository;
    }

    public function store(Store $request)
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
}
