<?php

namespace App\Http\Controllers\API;

use App\Slot;
use App\Branch;
use App\Service;
use App\Appointment;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use App\Models\AppointmentOnsite;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function getAllByBranchId(Request $request, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $branch = Branch::find($branch_id);
        $booking_form = $branch->BranchConfiguration->template_booking_form;
        $type = [
                'appointment' => 'appointment',
                'onsite' => 'appointment-onsite',
        ];
        $queueType = $type[$branch->queue_type];

        $query = Service::where('branch_id', $branch_id)
                            ->where('is_disable',false);

        if ($request->filled('service_category_id')) {
            $query->where('service_category_id', $request->service_category_id);
        }
                            
        $services = $query->get();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = $this->getFilledSlot($queueType, [
                'service_id' => $service->id,
                'date' => $dateNow
            ]);

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->template_form_booking = $service->template_form_booking ?? $booking_form;
            $service->slots = $slots->get();
            $service->filledSlot = $filledSlot;
            $service->totalSlot = $slots->sum('max_slots');
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data' => $services,
        ]);
    }

    public function getById(Request $request, $id)
    {
        $service = Service::with(['Slot','Branch'])
                            ->where('id', $id)
                            ->first()
                            ->makeHidden('Branch');
        $date = $request->date ?? date('Y-m-d');
        $type = [
                'appointment' => 'appointment',
                'onsite' => 'appointment-onsite',
        ];
        $queueType = $request->queue_type ?? $type[$service->Branch->queue_type];

        foreach ($service->slot as $slot) {
            $slot->filled_slot = $this->getFilledSlot($queueType, [
                'service_id' => $service->id,
                'slot_id' => $slot->id,
                'date' => $date
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'get service by id',
            'data' => $service
        ]);
    }

    public function getFilledSlot($queue_type, $params)
    {
        if ($queue_type == 'appointment') {
            return Appointment::withoutCanceled()->whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }

        if ($queue_type == 'exhibition') {
            return Exhibition::whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }

        if($queue_type == 'appointment-onsite') {
            return AppointmentOnsite::whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->when(isset($params['slot_id']), function ($q) use ($params) {
                    $q->where('slot_id', $params['slot_id']);
                })
                ->where('date', $params['date'])
                ->count();
        }
    }

    public function getByDepartmentId($id)
    {
        return Service::where('department_id', $id)->get();
    }
}
