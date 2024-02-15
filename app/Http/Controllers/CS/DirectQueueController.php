<?php

namespace App\Http\Controllers\CS;

use App\DirectQueue;
use App\WorkstationService;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CS\StoreDirectQueue;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\DirectQueueRepositoryInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Events\OnsiteQueueUpdated;
use App\Events\QueueStatusUpdated;
use App\Events\OnsiteQueueCalled;

class DirectQueueController extends Controller
{
    private DirectQueueRepositoryInterface $appointment_onsite_repository;

    public function __construct(DirectQueueRepositoryInterface $appointment_onsite_repository)
    {
        $this->appointment_onsite_repository = $appointment_onsite_repository;
    }

    private function InitQuery()
    {
        return DirectQueue::query()
                    ->addSelect([
                        'vct_priority' => WorkstationService::query()
                            ->select('priority')
                            ->whereColumn('service_id', 'direct_queues.service_id')
                            ->where('workstation_id', Auth::user()->WorkstationVct->workstation_id)
                            ->limit(1)
                    ])
                    ->with('Service')
                    ->whereDate('direct_queues.created_at', Date('Y-m-d'))
                    ->whereNotIn('status', ['end served', 'no show'])
                    ->orderBy('direct_queues.requeue_count', 'ASC')
                    ->orderBy('vct_priority', 'ASC')
                    ->orderBy('direct_queues.priority', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->orderBy('direct_queues.queue_no', 'ASC');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $directQueues = $this->InitQuery();

        $directQueues->when($request->keyword, function($query) use ($request){
            return $query->where(function($query) use ($request){
                return $query->where('name', 'ilike', '%'.$request->keyword.'%')->orWhere('queue_no', $request->keyword);
            });
        });

        $requesterWorkstationId = Auth::user()->WorkstationVct->workstation_id;
        $data = $directQueues->get()->filter(function($directQueue) use ($requesterWorkstationId) {
            return $directQueue->vct_priority &&  ($directQueue->status !== 'served' || $directQueue->workstation_id === $requesterWorkstationId);
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'get all direct queues by today',
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $workstationServices = WorkstationService::whereWorkstationId(Auth::user()->WorkstationVct->workstation_id)->get();
        return view('cs.directQueue.create')->withServices($workstationServices);
    }

    public function workstationServices()
    {
        $workstationServices = WorkstationService::whereHas('Workstation.WorkstationVct', function($query){
            return $query->whereVctId(Auth::id());
        })->with('Service')->get();

        return response()->json([
            'success' => true,
            'message' => 'get all workstation service',
            'data' => $workstationServices
        ]);
    }

    public function getAllWorkstationServiceByBranch() {
        $userIDs = User::select('id')->where([
            'branch_id' => Auth::user()->branch_id,
            'role' => 'cs',
        ])->get();

        $vctIds = [];
        foreach ($userIDs as $value) {
            array_push($vctIds, $value->id);
        }

        $workstationServices = WorkstationService::whereHas('Workstation.WorkstationVct', function($query) use ($vctIds) {
            return $query->whereIn('vct_id', $vctIds);
        })->with('Service')->get();

        return response()->json([
            'success' => true,
            'message' => 'get all service on branch',
            'data' => DirectQueueController::unique_key($workstationServices, 'service_id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDirectQueue $request)
    {
        try {
            $workstation_service = WorkstationService::find($request->workstation_service_id);

            $data = $request->all();
            $data['workstation_id'] = $workstation_service->workstation_id;
            $data['user_id'] = Auth::id();
            $data['service_id'] = $workstation_service->service_id;
            $data['direct_queue_channel'] = 'Web';

            $direct_queue = $this->appointment_onsite_repository->store($data);

            event(new VCTDirectQueueEvent($direct_queue, Auth::user()->branch_id));
            event(new DirectQueueEvent($direct_queue, Auth::user()->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            $request->session()->flash('success', __('Direct Queue Has Been Created, Queue no: :no', ['no' => $direct_queue->queue_no]));
            return redirect()->route('cs.directQueue.show', [
                'directQueue' => $direct_queue->id
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect(route('cs.directQueue.create'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DirectQueue  $directQueue
     * @return \Illuminate\Http\Response
     */
    public function show($direct_queue_id)
    {
        setlocale(LC_TIME, 'id_ID');

        $direct_queue = DirectQueue::find($direct_queue_id);
        $workstation = $direct_queue->Workstation;
        $service = $direct_queue->Service;

        $data = [
            'queue_no' => $direct_queue->queue_no,
            'date' => date('j F Y', strtotime($direct_queue->created_at)),
            'booking_code' => $direct_queue->booking_code,
            'workstation_label' => $workstation->label,
            'service_name' => $service->name,
            'status' => __($direct_queue->status)
        ];

        return view('cs.directQueue.show', [
            'direct_queue' => $data,
            'qr_code' => $this->get_queue_status_qr($direct_queue->id)
        ]);
    }

    private function get_queue_status_qr($queue_id)
    {
        return QrCode::size(180)->generate(
            url('customer/' . Auth::user()->branch_id . '/' . Auth::user()->Branch->queue_type . '/booking-status/' . $queue_id)
        );
    }

    public function monitor()
    {
        return view('cs.directQueue.monitor');
    }

    private function checkPreviousQueue($directQueue, $isSkip = false)
    {
        $query = $this->InitQuery();

        if ($directQueue->status == 'requeue' && !$isSkip) {
            $queues = $query
                ->whereIn('status', ['served', 'waiting'])
                ->where('id', '!=', $directQueue->id)
                ->where('workstation_id', '=', $directQueue->workstation_id)
                ->exists();
        }else if ($isSkip) {
            $queues = $query
                ->whereStatus('served')
                ->where('id', '!=', $directQueue->id)
                ->where('workstation_id', '=', $directQueue->workstation_id)
                ->exists();
        } else {
            $query->whereNotIn('status', ['end served', 'no show', 'requeue']);
            $requesterWorkstationId = Auth::user()->WorkstationVct->workstation_id;
            $queues = $query->get()->filter(function($item) use ($requesterWorkstationId){
                return $item->vct_priority &&  ($item->status !== 'served' || $item->workstation_id === $requesterWorkstationId);
            })->values();

            $queues = $queues[0] ? $queues[0]->queue_no != $directQueue->queue_no : false;
        }

        return $queues;
    }

    public function onCall(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'is_skip' => 'nullable|boolean',
            'service_id' => 'required|integer|exists:services,id'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        // check the queue no with created date is today
        $directQueue = DirectQueue::where('queue_no', $request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereNotIn('status', ['no show', 'end served'])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => null
            ], 404);
        }

        $workstation_old = $directQueue->workstation_id;
        $directQueue->update([
            'workstation_id' => $request->workstation_id,
        ]);
        $directQueue->refresh();

        // check queue can called if previous queue end served
        if ($this->checkPreviousQueue($directQueue, $request->is_skip)) {
            return response()->json([
                'success' => false,
                'message' => 'Previous queue not finished',
                'data' => null
            ], 400);
        }

        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->vct_id = Auth::id();
            $directQueue->status = 'no show';
            $directQueue->called_at = $directQueue->called_at ? $directQueue->called_at : Date('Y-m-d H:i:s') ;
            $directQueue->done_at = Date('Y-m-d H:i:s');
            $directQueue->save();

            event(new QueueStatusUpdated([
                'queue_no' => $directQueue->queue_no,
                'status' => 'no show',
                'branch_id' => Auth::user()->branch_id,
                'workstation_id' => $directQueue->workstation_id
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => null
            ], 400);
        }

        $workstation_service = Auth::user()
            ->WorkstationVct
            ->Workstation
            ->WorkstationService()
            ->where('service_id', $directQueue->service_id)
            ->first();

        $directQueue->workstation_service_id = $workstation_service->id;
        $directQueue->workstation_id = Auth::user()->WorkstationVct->workstation_id;
        $directQueue->status = 'served';
        $directQueue->recall_count = $directQueue->recall_count > 0 ? $directQueue->recall_count + 1 : 0;
        $directQueue->called_at = null;
        $directQueue->call_time = Date('Y-m-d H:i:s');
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'served',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
            event(new OnsiteQueueCalled($directQueue));
        }

        if ($directQueue->fcm_id) {
            fcm()
                ->to([$directQueue->fcm_id])
                ->priority('high')
                ->timeToLive(0)
                ->data([
                    'title' => 'KYOO',
                    'body' => "Antrian " . $directQueue->queue_no . " sedang dipanggil. Mohon ke " . Auth::user()->WorkstationVct->Workstation->label,
                    'data' => [
                        'url' => '/customer/' . Auth::user()->branch_id . '/onsite/booking-status/' . $directQueue->id
                    ]
                ])
                ->send();
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onServed(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'is_skip' => 'nullable|boolean',
            'service_id' => 'required|integer|exists:services,id'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        // check the queue no with created date is today
        $directQueue = DirectQueue::where('queue_no', $request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereNotIn('status', ['no show', 'end served'])
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => null
            ], 404);
        }

        $workstation_old = $directQueue->workstation_id;
        $directQueue->update([
            'workstation_id' => $request->workstation_id,
        ]);
        $directQueue->refresh();

        // check queue can called if previous queue end served
        if ($this->checkPreviousQueue($directQueue, $request->is_skip)) {
            return response()->json([
                'success' => false,
                'message' => 'Previous queue not finished',
                'data' => null
            ], 400);
        }

        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->vct_id = Auth::id();
            $directQueue->status = 'no show';
            $directQueue->done_at = Date('Y-m-d H:i:s');
            $directQueue->save();

            event(new QueueStatusUpdated([
                'queue_no' => $directQueue->queue_no,
                'status' => 'no show',
                'branch_id' => Auth::user()->branch_id,
                'workstation_id' => $directQueue->workstation_id
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => null
            ], 400);
        }

        $workstation_service = Auth::user()
            ->WorkstationVct
            ->Workstation
            ->WorkstationService()
            ->where('service_id', $directQueue->service_id)
            ->first();

        $directQueue->workstation_service_id = $workstation_service->id;
        $directQueue->workstation_id = Auth::user()->WorkstationVct->workstation_id;
        $directQueue->status = 'served';
        $directQueue->recall_count = $directQueue->recall_count > 0 ? $directQueue->recall_count + 1 : 0;
        $directQueue->called_at = Date('Y-m-d H:i:s');
        $directQueue->waiting_duration = $request->waiting_duration;
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'served',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
            event(new OnsiteQueueCalled($directQueue));
        }

        if ($directQueue->fcm_id) {
            fcm()
                ->to([$directQueue->fcm_id])
                ->priority('high')
                ->timeToLive(0)
                ->data([
                    'title' => 'KYOO',
                    'body' => "Antrian " . $directQueue->queue_no . " sedang dipanggil. Mohon ke " . Auth::user()->WorkstationVct->Workstation->label,
                    'data' => [
                        'url' => '/customer/' . Auth::user()->branch_id . '/onsite/booking-status/' . $directQueue->id
                    ]
                ])
                ->send();
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onRecall(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'service_id' => 'required|integer|exists:services,id'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereDate('created_at', Date('Y-m-d'))
            ->first();

        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->status = 'no show';
            $directQueue->done_at = Date('Y-m-d H:i:s');
            $directQueue->save();

            event(new QueueStatusUpdated([
                'queue_no' => $directQueue->queue_no,
                'status' => 'no show',
                'branch_id' => Auth::user()->branch_id,
                'workstation_id' => $directQueue->workstation_id
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => $directQueue
            ], 400);
        }

        $directQueue->status = $directQueue->recall_count + 1 >= Auth::user()->Branch->BranchConfiguration->maximum_recall ? 'no show' : 'served';
        $directQueue->recall_count = $directQueue->recall_count + 1;
        $directQueue->called_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'recall',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
            event(new OnsiteQueueCalled($directQueue));
        }

        if ($directQueue->fcm_id) {
            fcm()
                ->to([$directQueue->fcm_id])
                ->priority('high')
                ->timeToLive(0)
                ->data([
                    'title' => 'KYOO',
                    'body' => "Antrian " . $directQueue->queue_no . " sedang dipanggil. Mohon ke " . Auth::user()->WorkstationVct->Workstation->label,
                    'data' => [
                        'url' => '/customer/' . Auth::user()->branch_id . '/onsite/booking-status/' . $directQueue->id
                    ]
                ])
                ->send();
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onRequeue(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues,queue_no',
            'service_id' => 'required|integer|exists:services,id'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereDate('created_at', Date('Y-m-d'))
            ->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }

        // check if queue requeue_count on limit
        if ($directQueue->requeue_count >= Auth::user()->Branch->BranchConfiguration->maximum_requeue_count) {
            return response()->json([
                'success' => false,
                'message' => 'Queue requeue has on limited',
                'data' => $directQueue
            ], 400);
        }

        $directQueue->status = 'requeue';
        $directQueue->requeue_count = $directQueue->requeue_count + 1;
        $directQueue->recall_count = 0;
        $directQueue->called_at = Date('Y-m-d H:m:i');

        /**
         * disabled this flow
         */
        // $lastQueue = DirectQueue::where('vct_id', Auth::id())->where('workstation_service_id', $directQueue->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->first();
        // $directQueue['queue_no'] = (int) $lastQueue->queue_no + 1;

        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'requeue',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onEndServed(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'service_id' => 'required|integer|exists:services,id'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereDate('created_at', Date('Y-m-d'))
            ->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        $directQueue->status = 'end served';
        $directQueue->done_at = Date('Y-m-d H:i:s');
        $directQueue->serving_duration = $request->serving_duration;
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'end served',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on End Served',
            'data' => $directQueue
        ]);
    }

    public function onNoShow(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'service_id' => 'required|integer|exists:services,id'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereDate('created_at', Date('Y-m-d'))
            ->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        $directQueue->status = 'no show';
        $directQueue->done_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'no show',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $directQueue->workstation_id
        ]));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on No Show',
            'data' => $directQueue
        ]);
    }

    public function onTransfer(Request $request)
    {
        $rules = [
            'queue_no' => 'required|alpha_num|min:1|exists:direct_queues',
            'workstation_service_id' => 'required|integer|min:1|exists:workstation_services,id',
            'service_id' => 'required|integer|exists:services,id'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $oldDirectQueue = DirectQueue::where('queue_no' ,$request->queue_no)
            ->where('service_id', $request->service_id)
            ->whereDate('created_at', Date('Y-m-d'))
            ->first();
        if (!$oldDirectQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }

        $oldDirectQueue->status = 'end served';
        $oldDirectQueue->done_at = Date('Y-m-d H:i:s');
        $oldDirectQueue->serving_duration = $request->serving_duration;
        $oldDirectQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $oldDirectQueue->queue_no,
            'status' => 'end served',
            'branch_id' => Auth::user()->branch_id,
            'workstation_id' => $oldDirectQueue->workstation_id
        ]));

        if ($oldDirectQueue->client_id) {
            event(new OnsiteQueueUpdated($oldDirectQueue));
        }

        $workstation_service = WorkstationService::find($request->workstation_service_id);

        $data = $request->all();
        $data['name'] = $oldDirectQueue->name;
        $data['phone'] = $oldDirectQueue->phone;
        $data['workstation_id'] = $workstation_service->workstation_id;
        $data['user_id'] = Auth::id();
        $data['service_id'] = $workstation_service->service_id;
        $data['old_service_id'] = $oldDirectQueue->service_id;
        $data['direct_queue_channel'] = 'Web';

        $directQueue = $this->appointment_onsite_repository->transfer($data);
        $oldDirectQueue->new_service_id = $directQueue->service_id;
        $oldDirectQueue->save();

        event(new VCTDirectQueueEvent($directQueue, Auth::user()->branch_id));
        event(new DirectQueueEvent($directQueue, Auth::user()->branch_id));

        if ($directQueue->client_id) {
            event(new OnsiteQueueUpdated($directQueue));
        }

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Transfer',
            'data' => $directQueue
        ]);
    }

    private function unique_key($array,$removeKey){
        $new_array = array();
        foreach($array as $key=>$value){
          if(!isset($new_array[$value[$removeKey]])){
            $new_array[$value[$removeKey]] = $value;
          }
        }
        return array_values($new_array);
    }
}
