<?php

namespace App\Http\Controllers\Device;

use App\User;
use App\Branch;
use Carbon\Carbon;
use App\DirectQueue;

use App\Workstation;
use App\Models\TVToken;
use App\WorkstationService;
use Illuminate\Support\Str;
use App\BranchConfiguration;
use App\Models\SecretKeyAPi;
use Illuminate\Http\Request;
use App\Models\WebkioskToken;
use App\Models\AppointmentOnsite;
use App\Events\OnsiteQueueUpdated;
use App\Models\FeatureSubscription;

use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\Device\StoreDirectQueue;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Interfaces\WebKioskConfigurationRepositoryInterface;

class HomeController extends Controller
{
    private WebKioskConfigurationRepositoryInterface $webKioskConfigurationRepository;
    private DirectQueueRepositoryInterface $onsite_repository;

    public function __construct(
        WebKioskConfigurationRepositoryInterface $webKioskConfigurationRepository,
        DirectQueueRepositoryInterface $onsite_repository
    )
    {
        $this->webKioskConfigurationRepository = $webKioskConfigurationRepository;
        $this->onsite_repository = $onsite_repository;
    }


    public function index()
    {
        $branch = auth()->user()->Branch;

        $tv_config = $branch && $branch->TVconfiguration ? $branch->TVconfiguration->TVToken : null;
    $webkiosk_config = $branch && $branch->WebkioskConfiguration ? $branch->WebkioskConfiguration->WebkioskToken : null;

   
        $tv_token = $tv_config ? $tv_config->token : Str::random(12);
        $webkiosk_token = $webkiosk_config ? $webkiosk_config->token : Str::random(12);

        return view('device.home', compact(['tv_token','webkiosk_token']));
    }


    public function webMonitor()
    {
        $branchID = request()->branch_id;
        $branch = Branch::with(['BranchConfiguration', 'Departments'])->findOrFail($branchID);
        $TVConfiguration = $branch->TVConfiguration;
        $TVToken = TVToken::where('tv_configuration_id', $TVConfiguration->id)->first();
        $customLayoutConfig = $TVConfiguration->customLayoutConfiguration2;
        $display_duration = (int) $TVConfiguration->display_duration * 1000;

        if(!$TVToken) {
            $TVToken = TVToken::create([
                'tv_configuration_id' => $TVConfiguration->id,
                'token' => request()->token
            ]);
        } elseif ($TVToken->token != request()->token) {
            abort(403);
        }

        if ($branch->BranchType->is_appointment) {
            $encryptBranchId = Crypt::encrypt($branchID);
            $url = route('appointments.signage', ['branch_id' => $encryptBranchId, 'token' => request()->token]);

            return redirect($url);
        }

        $features = FeatureSubscription::with('AdditionalFeature')
            ->where('branch_id', $branch->id)
            ->get();

        if (
            $branch->BranchType->is_direct_queue
            && $branch->BranchConfiguration->template_signage !== 'standard-ui'
        ) {
            $workstations = Workstation::whereIn(
                'department_id',
                $branch->Departments->pluck('id')->toArray(),
            )
            ->take(6)
            ->orderBy('label')
            ->get();

            if($branch->BranchConfiguration->template_signage === 'custom-layout-1') {
                return view('device.signage.custom-1UI', [
                    'branch' => $branch,
                    'features' => $features,
                    'workstations' => $workstations,
                ]);
            } elseif($branch->BranchConfiguration->template_signage === 'custom-layout-2') {
                return view('device.signage.custom-2UI', [
                    'branch' => $branch,
                    'features' => $features,
                    'display_duration' => $display_duration,
                    'workstations' => $workstations,
                    'customLayoutConfig' => $customLayoutConfig
                ]);
            } else {
                return view('device.signage.custom-3UI', [
                    'branch' => $branch,
                    'features' => $features,
                    'customLayoutConfig' => $customLayoutConfig
                ]);
            }
        }

        return view('device.signage.standardUI', [
            'branch' => $branch,
            'features' => $features
        ]);
    }

    public function workstationList(Branch $branch)
    {
        $workstations = Workstation::whereIn(
                            'department_id',
                            $branch->Departments->pluck('id')->toArray(),
                        )->whereHas('DirectQueues', function($query) {
                            $query->where(function ($query) {
                                        $query->where('status', 'served')
                                            ->orWhere(function ($query) {
                                                $query->where('status', 'waiting')
                                                        ->whereNotNull('called_at');
                                            });
                                    })
                                ->whereDate('created_at', date('Y-m-d'));
                        })
                        ->with(['DirectQueues' => function($query) {
                            $query->where('status', 'served')
                                ->whereDate('created_at', date('Y-m-d'))
                                ->latest('updated_at')
                                ->limit(1);
                        }])
                        ->orderBy(
                            function ($query) {
                                $query->from('direct_queues')
                                    ->whereColumn('workstations.id', 'direct_queues.workstation_id')
                                    ->where(function ($query) {
                                        $query->where('status', 'served')
                                    ->whereDate('created_at', date('Y-m-d'))
                                    ->latest('updated_at')
                                    ->limit(1);
                                })
                                ->whereDate('created_at', date('Y-m-d'))
                                ->orderBy('updated_at', 'desc')
                                ->limit(1)
                                ->select('updated_at');
                            },
                            'desc'
                        )
                        ->get();

        foreach($workstations as $workstation) {
            $queue = DirectQueue::where('workstation_id', $workstation->id)
                                ->where('status', 'served')
                                ->whereDate('created_at', date('Y-m-d'))
                                ->first();
            $workstation->queue_no = $queue->queue_no;
        }

        return response()->json([
            'success' => true,
            'message' => 'get queues by branch id',
            'data' => $workstations
        ]);
    }

    public function directQueueList($branch_id)
    {
        $queues = DirectQueue::whereHas('Service', function ($query) use ($branch_id) {
            return $query->where('branch_id', $branch_id);
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->whereNotIn('status', ['end served', 'no show'])
            ->orderBy('queue_no')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'get queues by branch id',
            'data' => $queues
        ]);
    }

    public function directQueueServed($branch_id)
    {
        $queues = DirectQueue::whereHas('Service', function ($query) use ($branch_id) {
            return $query->where('branch_id', $branch_id);
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->where('status', 'served')
            ->orderBy('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'get queues served by branch id',
            'data' => $queues
        ]);
    }

    public function webKioskUI()
    {
        $branchID = request()->branch_id;
        $configuration = $this->webKioskConfigurationRepository->GetOneConfigurationByBranchID($branchID);
        $branch = Branch::findOrFail($branchID);
        $WebkioskConfigurationID = $branch->WebkioskConfiguration->id;
        $WebKioskToken = WebkioskToken::where('webkiosk_configuration_id', $WebkioskConfigurationID)->first();

        $layoutConfigurationMapping = [
            1 => null,
            2 => 'layoutConfiguration2',
            3 => 'layoutConfiguration3',
            4 => 'layoutConfiguration4',
        ];

        $layoutConfig = $configuration->{$layoutConfigurationMapping[$configuration->layout->id]} ?? null;

        if(!$WebKioskToken) {
            $WebKioskToken = WebkioskToken::create([
                'webkiosk_configuration_id' => $WebkioskConfigurationID,
                'token' => request()->token
            ]);
        } elseif ($WebKioskToken->token != request()->token) {
            abort(403);
        }

        return view(
            'device.webKioskUI',
            [
                'branch' => $branch,
                'address' => (object)[
                    'regency' => $branch->Regency->name,
                    'province' => $branch->Regency->province->name
                ],
                'layoutCode' => $configuration->layout->code ?? 'layout_1',
                'layoutConfig' => $layoutConfig,
                'qr' => "data:image/svg+xml;base64,".base64_encode(QrCode::size(180)->generate(
                    url('customer/' . $branchID . '/' . $branch->queue_type . '/services')
                )),
                'isAllowWA' => $branch->BranchConfiguration->wa_notification,
                'activeMenus' => $configuration->active_menus
            ]
        );
    }

    public function getAllWorkstationServiceByBranch($branch_id) {
        $userIDs = User::select('id')
            ->where('branch_id', $branch_id)
            ->whereIn('role', ['cs', 'spv'])
            ->get();

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
            'data' => HomeController::unique_key($workstationServices,'service_id'),
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
            $user = User::find($request->user_id);
            $workstation_service = WorkstationService::find($request->workstation_service_id);

            $data = $request->all();
            // $data['workstation_id'] = $workstation_service->workstation_id;
            // $data['user_id'] = $user->id;
            $data['service_id'] = $workstation_service->service_id;
            $data['direct_queue_channel'] = 'Device';

            $direct_queue = $this->onsite_repository->store($data);
            $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                                                        ->whereStatus('waiting')
                                                        ->whereDate('created_at', date('Y-m-d'))
                                                        ->count();
            
            event(new VCTDirectQueueEvent($direct_queue, $user->branch_id));
            event(new DirectQueueEvent($direct_queue, $user->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }
            $branchID = $user->branch->id;
            $client = BranchConfiguration::where('branch_id',$branchID)->first();
            $tokenAPI = SecretKeyAPi::where('branch_id', $branchID)->first();
            $webhookMessage = "You need an Webhook Url or Activate the feature!";

            if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
                $webhookMessage = "Webhook Send!";

                $timezone = null;
                if($user->branch && $user->branch->timezone){
                    if($user->branch && $user->branch->timezone) {
                        switch($user->branch->timezone) {
                            case 'WIB':
                                $timezone = 'GMT+7';
                                break;
                            case 'WITA':
                                $timezone = 'GMT+8';
                                break;
                            case 'WIT':
                                $timezone = 'GMT+9';
                                break;
                            default:
                                $timezone = null;
                                break;
                        }
                    }
                }
    
                $webhookData = [
                    'event_type' => 'onsite_checkin_booking',
    
                    'queue' => (object)[
                            'id' => $direct_queue->id,
                            'service_id' => $direct_queue->service_id,
                            'branch_id' =>  $branchID,
                            'booking_code' =>  strtoupper($direct_queue->booking_code),
                            'service_type' => 'Onsite Queue',
                            'check_in_status' => true,
                            'check_in_date' => $direct_queue->created_at,
                            'created_at' => $direct_queue->created_at,
                        ],
                        'branch' => (object)[
                            'id' =>  $branchID,
                            'name' => $user->branch->name,
                        ],
                        'service' => (object)[
                            'id' => $direct_queue->service_id,
                            'name' => $direct_queue->service->name,
                            'branch_id' => $direct_queue->service->Branch->id,
                            'branch_name' => $direct_queue->service->Branch->name,
                        ]
                ];
                $webhookUpdatedData = (object) $webhookData;
                
               $this->sendWebhook($client, $webhookUpdatedData);
                
            }else{
                $webhookMessage = "There's no Webhook Url or The feature was inactive";
            }

            return response()->json([
                'success' => true,
                'data' => $direct_queue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 412);
        }
    }


    public function storeByBookingCode(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            $branchConfig = $user->branch->branchConfiguration;

            $checked_in_appointment = AppointmentOnsite::where('booking_code', strtolower($request->booking_code))
            ->where('is_used', true)
            ->whereDate('date', date('Y-m-d'))
            ->first();

            if($checked_in_appointment) {
                $direct_queue = DirectQueue::where('appointment_onsite_id', $checked_in_appointment->id)->first();
                $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                ->whereStatus('waiting')
                ->whereDate('created_at', date('Y-m-d'))
                ->where('created_at', '<=', $direct_queue->created_at)
                ->count();

                return response()->json([
                    'success' => true,
                    'data' => $direct_queue
                ]);
            }

            $appointment_onsite = AppointmentOnsite::where('booking_code', strtolower($request->booking_code))
            ->where('is_used', false)
            ->whereDate('date', '>=', date('Y-m-d'))
            ->first();
            

            if(!$appointment_onsite) {
                throw new \Exception(__('Booking code not found or expired'), 10003);
            } elseif ($appointment_onsite->date != date('Y-m-d')) {
                throw new \Exception(__('The booking code is not valid yet, please check the booking date'), 10004);
            }

            if ($branchConfig->check_in_rule != 0) {
                $startTime =  Carbon::createFromFormat('H:i:s', $appointment_onsite->start_time);

                $allowedCheckInTime = $startTime->subHours($branchConfig->check_in_rule);
    
                if (now()->format('H:i:s') < $allowedCheckInTime->format('H:i:s')) {
                    throw new \Exception(__('Check-in is done ') . $branchConfig->check_in_rule . __(' hours before the service opens. You can check in at ') . $allowedCheckInTime->format('H:i') . ".", 10005);
                }
            }

            $data = $appointment_onsite->toArray();
            // $data['user_id'] = $user->id;
            // $data['vct_id'] = $request->vct_id;
            $data['direct_queue_channel'] = 'Device';
            $data['priority'] = 1;
            $data['appointment_onsite_id'] = $appointment_onsite->id;

            $direct_queue = $this->onsite_repository->store($data);
            $direct_queue->total_waiting = DirectQueue::whereServiceId($direct_queue->service_id)
                                                        ->whereStatus('waiting')
                                                        ->whereDate('created_at', date('Y-m-d'))
                                                        ->count();

            event(new VCTDirectQueueEvent($direct_queue, $user->branch_id));
            event(new DirectQueueEvent($direct_queue, $user->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            $appointment_onsite->update([
                'is_used' => true
            ]);

            $branchID = $user->branch->id;
            $client = BranchConfiguration::where('branch_id',$branchID)->first();
            $tokenAPI = SecretKeyAPi::where('branch_id', $branchID)->first();
            $webhookMessage = "You need an Webhook Url or Activate the feature!";

            if ($client->webhook_url && $tokenAPI->secret_token && $tokenAPI->is_active){
                $webhookMessage = "Webhook Send!";
                $startTime = $appointment_onsite->start_time;
                $endTime = $appointment_onsite->end_time;
    
                if (strlen($startTime) === 5) { // Jika dalam format H:i
                    $startTime = Carbon::createFromFormat('H:i', $startTime)->format('H:i:s');
                } else {
                    $startTime = Carbon::createFromFormat('H:i:s', $startTime)->format('H:i:s');
                }
                
                if (strlen($endTime) === 5) { // Jika dalam format H:i
                    $endTime = Carbon::createFromFormat('H:i', $endTime)->format('H:i:s');
                } else {
                    $endTime = Carbon::createFromFormat('H:i:s', $endTime)->format('H:i:s');
                }
    
                
                $timezone = null;
                if($user->branch && $user->branch->timezone){
                    if($user->branch && $user->branch->timezone) {
                        switch($user->branch->timezone) {
                            case 'WIB':
                                $timezone = 'GMT+7';
                                break;
                            case 'WITA':
                                $timezone = 'GMT+8';
                                break;
                            case 'WIT':
                                $timezone = 'GMT+9';
                                break;
                            default:
                                $timezone = null;
                                break;
                        }
                    }
                }
    
                $webhookData = [
                    'event_type' => 'onsite_checkin_booking',
    
                    'queue' => (object)[
                            'id' => $appointment_onsite->id,
                            'service_id' => $appointment_onsite->service_id,
                            'branch_id' =>  $branchID,
                            'booking_code' =>  strtoupper($appointment_onsite->booking_code),
                            'service_type' => 'Appointment Onsite Queue',
                            'check_in_status' => $appointment_onsite->is_used,
                            'check_in_date' => $appointment_onsite->updated_at,
                            'created_at' => $appointment_onsite->created_at,
                        ],
                        'branch' => (object)[
                            'id' =>  $branchID,
                            'name' => $user->branch->name,
                        ],
                        'service' => (object)[
                            'id' => $appointment_onsite->service_id,
                            'name' => $appointment_onsite->service->name,
                            'branch_id' => $appointment_onsite->service->Branch->id,
                            'branch_name' => $appointment_onsite->service->Branch->name,
                        ]
                ];
                $webhookUpdatedData = (object) $webhookData;
                
               $this->sendWebhook($client, $webhookUpdatedData);
                
            }else{
                $webhookMessage = "There's no Webhook Url or The feature was inactive";
            }


            return response()->json([
                'success' => true,
                'data' => $direct_queue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 412);
        }
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
    protected function sendWebhook($client, $webhookUpdatedData)
    {
        $guzzle = new \GuzzleHttp\Client();  
        $tokenAPI = SecretKeyAPi::where('branch_id', $client->branch_id)->first();
        try {

            $response = $guzzle->post($client->webhook_url, [
                'headers' => [
                    'x-secret-token' => $tokenAPI->secret_token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $webhookUpdatedData
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Webhook failed with status: ' . $response->getStatusCode());
            }

            return response()->json([
                'status' => 'success',
               ]);

        } catch (\Exception $e) {
           return response()->json([
            'status' => 'error',
            'message' =>  $e->getMessage()
           ]);
        }
    }
}
