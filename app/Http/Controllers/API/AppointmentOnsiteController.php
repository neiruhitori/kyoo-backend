<?php

namespace App\Http\Controllers\API;

use App\Branch;
use App\DirectQueue;
use App\Http\Controllers\Controller;
use App\Interfaces\AppointmentOnsiteRepositoryInterface;
use App\Service;
use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Models\AppointmentOnsite;
use App\Http\Resources\AppointmentOnsite\Detail as AppointmentOnsiteDetail;
use Illuminate\Http\Request;

class AppointmentOnsiteController extends Controller
{
    private AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository;

    public function __construct(AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository)
    {
        $this->appointmentOnsiteRepository = $appointmentOnsiteRepository;
    }

    public function index(Branch $branch, Request $request)
    {
        $services = Service::where('branch_id', $branch->id)
            ->get();
        $branchConfiguration = $branch->BranchConfiguration;
        $schedule = $branch->schedule->where('day', $request->day)->first();
        $startTime = $schedule ? strtotime($schedule->start_time) : null;
        $endTime = $schedule ? strtotime($schedule->end_time) : null;
        $timeInterval = $branchConfiguration->time_interval * 60 * 60;

        foreach ($services as $service) {
            $service->available_slot = 0;

            if($startTime && $endTime) {
                for ($i = $startTime; $i < $endTime; $i += $timeInterval) {
                    $service->available_slot++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'get direct queue services by branch id',
            'data' => $services,
        ]);
    }

    public function show(AppointmentOnsite $appointmentOnsite)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail appointment onsite',
            'data' => new AppointmentOnsiteDetail($appointmentOnsite)
        ]);
    }

    public function slots(Branch $branch, Request $request)
    {
        $branchConfiguration = $branch->BranchConfiguration;
        $schedule = $branch->schedule->where('day', $request->day)->first();
        $startTime = $schedule ? strtotime($schedule->start_time) : null;
        $endTime = $schedule ? strtotime($schedule->end_time) : null;
        $timeInterval = $branchConfiguration->time_interval * 60 * 60;
        $slots = [];

        if($startTime && $endTime) {
            for ($i = $startTime; $i < $endTime; $i += $timeInterval) {
                $slotStartTime = $i;
                $slotEndTime = $slotStartTime + $timeInterval;
                if ($slotEndTime > $endTime) {
                    $slotEndTime = $endTime;
                }

                $filled_slot = AppointmentOnsite::where('service_id', $request->service_id)->where('date', $request->date)->where('start_time', date('H:i:s', $slotStartTime))->where('end_time', date('H:i:s', $slotEndTime))->count();

                $slots[] = [
                    'start_time' => date('H:i', $slotStartTime),
                    'end_time' => date('H:i', $slotEndTime),
                    'filled_slot' => $filled_slot,
                    'max_slots' => $branchConfiguration->max_slots
                ];
            }
        }

        return response()->json([
                'success' => true,
                'message' => 'get schedule appointment onsite',
                'data' => $slots
        ]);
    }

    public function store(DirectQueueStore $request)
    {
        try {
            Service::find($request->service_id);

            $data = $request->all();
            $data['client_id'] = $request->cookie('client_id');

            $appointmentOnsite = $this->appointmentOnsiteRepository->store($data);

            return response()->json([
                'success' => true,
                'message' => 'appointment onsite created',
                'data' => $appointmentOnsite
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
