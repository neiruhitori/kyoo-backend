<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlotService;
use Illuminate\Support\Facades\Auth;
use App\Supports\DateFormat;

class TimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = SlotService::whereHas('Service.Department', function ($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })
            ->get()
            ->map(function ($slot) {
                return (object) [
                    'day_index' => (int) date('w', strtotime($slot->Slot->day)),
                    'day' => ucwords(DateFormat::daysLocale($slot->Slot->day)),
                    'service' => $slot->Service,
                    'time_slot' => "{$slot->Slot->start_time} - {$slot->Slot->end_time}",
                    'max_slots' => $slot->max_slots
                ];
            })
            ->sortBy('day_index');

        return view('adminBranch.timeSlot.index', [
            'timeSlots' => $timeSlots
        ]);
    }

    public function create()
    {
        return view('adminBranch.timeSlot.create');
    }

    public function store()
    {
        //
    }
}
