<?php

namespace App\Http\Controllers\Device;

use Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Branch;
use App\DirectQueue;
use App\User;
use App\WorkstationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CS\StoreDirectQueue;
use App\Interfaces\WebKioskConfigurationRepositoryInterface;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Models\FeatureSubscription;

use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Events\OnsiteQueueUpdated;

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
        $branch = Branch::with(['BranchConfiguration'])->findOrFail(Auth::user()->branch_id);
        $features = FeatureSubscription::with('AdditionalFeature')
            ->where('branch_id', $branch->id)
            ->get();

        return view('device.webMonitor', [
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
        $branchID = Auth::user()->branch_id;
        $configuration = $this->webKioskConfigurationRepository->GetOneConfigurationByBranchID($branchID);
        return view(
            'device.webKioskUI',
            [
                'branch' => Auth::user()->branch,
                'address' => (object)[
                    'regency' => Auth::user()->Branch->Regency->name,
                    'province' => Auth::user()->Branch->Regency->province->name
                ],
                'layoutCode' => $configuration->layout->code ?? 'layout_1',
                'layoutConfig' => $configuration->layoutConfiguration,
                'qr' => "data:image/svg+xml;base64,".base64_encode(QrCode::size(180)->generate(
                    url('customer/' . $branchID . '/' . Auth::user()->Branch->queue_type . '/services')
                )),
                'isAllowWA' => Auth::user()->Branch->BranchConfiguration->wa_notification,
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
            'data' => $workstationServices
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
}
