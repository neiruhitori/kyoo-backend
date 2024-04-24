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
use App\Slot;
use App\User;
use App\WorkstationService;
use Illuminate\Http\Request;

class AppointmentOnsiteController extends Controller
{
    private AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository;

    public function __construct(AppointmentOnsiteRepositoryInterface $appointmentOnsiteRepository)
    {
        $this->appointmentOnsiteRepository = $appointmentOnsiteRepository;
    }

    public function getAllByBranchId(Request $request, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $services = Service::where('branch_id', $branch_id)
                            ->get();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = $this->getFilledSlot([
                'service_id' => $service->id,
                'date' => $dateNow
            ]);

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->slots = $slots->get();
            $service->filledSlot = $filledSlot;
            $service->totalSlot = $slots->sum('max_slots');
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data' => $services
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

    public function getFilledSlot($params)
    {
        return AppointmentOnsite::whereHas('Slot', function ($query) use ($params) {
            $query->where('service_id', $params['service_id']);
        })
            ->when(isset($params['slot_id']), function ($q) use ($params) {
                $q->where('slot_id', $params['slot_id']);
            })
            ->where('date', $params['date'])
            ->count();
    }

    public function store(DirectQueueStore $request)
    {
        try {
            Service::find($request->service_id);
            $slot = Slot::find($request->slot_id);

            $data = $request->all();
            $data['phone'] = $this->cleanPhoneNumber($request->phone);
            $data['emergency_number'] = $this->cleanPhoneNumber($request->emergency_number);
            $data['client_id'] = $request->cookie('client_id');
            $data['start_time'] = $slot->start_time;
            $data['end_time'] = $slot->end_time;

            $appointmentOnsite = $this->appointmentOnsiteRepository->store($data);

            return response()->json([
                'success' => true,
                'message' => 'appointment onsite created',
                'data' => $appointmentOnsite
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }

    private function cleanPhoneNumber($phoneNumber) {
        $cleaned_phone = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (substr($cleaned_phone, 0, 1) == '0') {
            $cleaned_phone = '62' . substr($cleaned_phone, 1);
        } elseif (substr($cleaned_phone, 0, 3) == '620') {
            $cleaned_phone = '62' . substr($cleaned_phone, 3);
        } elseif (substr($cleaned_phone, 0, 1) == '8') {
            $cleaned_phone = '628' . substr($cleaned_phone, 1);
        }

        return $cleaned_phone;
    }
}
