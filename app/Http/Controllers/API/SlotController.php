<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\GetSlots;
use App\Slot;
use App\Appointment;

class SlotController extends Controller
{
    public function index(GetSlots $request)
    {
        $slots = Slot::where('service_id', $request->service_id)->get();

        foreach ($slots as $slot) {
            $filledSlot = Appointment::whereHas('Slot', function($query) use ($slot) {
                $query->where('slot_id', $slot->id);
            })->where('date', $request->date)->get();

            $slot->filledSlot = count($filledSlot);
        }

        return response()->json([
            'success' => true,
            'message' => 'get all slot by service id',
            'data' => $slots
        ]);
    }
}
