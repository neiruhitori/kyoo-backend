<?php

namespace App\Http\Controllers\Device;

use Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use App\Branch;
use App\DirectQueue;
use App\User;
use App\WorkstationService;
use App\Workstation;
use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreDirectQueue;
use App\Interfaces\WebKioskConfigurationRepositoryInterface;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Models\FeatureSubscription;
use Illuminate\Http\Request;

use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Events\OnsiteQueueUpdated;
use App\Models\AppointmentOnsite;
use App\Models\TVToken;
use App\Models\WebkioskToken;

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
        return view('device.home');
    }


    public function webMonitor()
    {
        $branchID = request()->branch_id;
        $branch = Branch::with(['BranchConfiguration', 'Departments'])->findOrFail($branchID);
        $TVConfiguration = $branch->TVConfiguration;
        $TVToken = TVToken::where('tv_configuration_id', $TVConfiguration->id)->first();
        $customLayoutConfig = $TVConfiguration->customLayoutConfiguration2;

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
            ->take(5)
            ->get();

            if($branch->BranchConfiguration->template_signage === 'custom-layout-1') {
                return view('device.signage.custom-1UI', [
                    'branch' => $branch,
                    'features' => $features,
                    'workstations' => $workstations,
                ]);
            } else {
                return view('device.signage.custom-2UI', [
                    'branch' => $branch,
                    'features' => $features,
                    'workstations' => $workstations,
                    'customLayoutConfig' => $customLayoutConfig
                ]);
            }
        }

        return view('device.signage.standardUI', [
            'branch' => $branch,
            'features' => $features
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

    public function webKioskUI()
    {
        $branchID = request()->branch_id;
        $configuration = $this->webKioskConfigurationRepository->GetOneConfigurationByBranchID($branchID);
        $branch = Branch::findOrFail($branchID);
        $WebkioskConfigurationID = $branch->WebkioskConfiguration->id;
        $WebKioskToken = WebkioskToken::where('webkiosk_configuration_id', $WebkioskConfigurationID)->first();
        $layoutConfig = $configuration->layout->id == 2 ? $configuration->layoutConfiguration2 : $configuration->layoutConfiguration3;

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
            $workstation_service = WorkstationService::find($request->workstation_service_id);

            $data = $request->all();
            $data['workstation_id'] = $workstation_service->workstation_id;
            $data['user_id'] = Auth::id();
            $data['service_id'] = $workstation_service->service_id;
            $data['direct_queue_channel'] = 'Device';

            $direct_queue = $this->onsite_repository->store($data);

            event(new VCTDirectQueueEvent($direct_queue, Auth::user()->branch_id));
            event(new DirectQueueEvent($direct_queue, Auth::user()->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            return response()->json([
                'success' => true,
                'data' => $direct_queue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 412);
        }
    }

    public function storeByBookingCode(Request $request)
    {
        try {
            $appointment_onsite = AppointmentOnsite::where('booking_code', strtolower($request->booking_code))
            ->where('is_used', false)
            ->where('date', date('Y-m-d'))
            ->firstOrFail();

            $workstation_service = WorkstationService::where('service_id', $appointment_onsite->service_id)->first();

            $data = $appointment_onsite->toArray();
            $data['workstation_id'] = $workstation_service->workstation_id;
            $data['workstation_service_id'] = $workstation_service->id;
            $data['user_id'] = Auth::id();
            $data['vct_id'] = $request->vct_id;
            $data['direct_queue_channel'] = 'Device';
            $data['priority'] = 1;

            $direct_queue = $this->onsite_repository->store($data);

            event(new VCTDirectQueueEvent($direct_queue, Auth::user()->branch_id));
            event(new DirectQueueEvent($direct_queue, Auth::user()->branch_id));

            if ($direct_queue->client_id) {
                event(new OnsiteQueueUpdated($direct_queue));
            }

            $appointment_onsite->update([
                'is_used' => true
            ]);

            return response()->json([
                'success' => true,
                'data' => $direct_queue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
}
