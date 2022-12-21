<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Slot;
use App\Department;
use App\Schedule;
use Illuminate\Support\Facades\Auth;
use App\Supports\DateFormat;

class TimeSlotController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $timeSlots = $this->getTimeSlots($branchId);

        return view('adminBranch.timeSlot.index', [
            'timeSlots' => $timeSlots
        ]);
    }

    private function getTimeSlots($branchId)
    {
        return Slot::whereHas('Department', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
            ->get()
            ->map(function ($slot) {
                $slot->day_index = date('w', strtotime($slot->day));
                $slot->day = ucwords(DateFormat::daysLocale($slot->day));
                $slot->quota = $slot->SlotService->sum('max_slots');

                return $slot;
            })
            ->sortBy(['day_index', 'start_time'])
            ->values();
    }

    public function create()
    {
        $days = $this->getDays();
        $departments = Department::where('branch_id', Auth::user()->branch_id)->get();

        return view('adminBranch.timeSlot.create', [
            'days' => $days,
            'departments' => $departments
        ]);
    }

    private function getDays()
    {
        return DateFormat::$DAYS;
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|integer',
            'day' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string'
        ]);

        $schedule = Schedule::where('day', $request->day)->first();

        if (
            !$schedule ||
            $schedule->status == 'closed' ||
            $request->start_time < $schedule->start_time ||
            $request->end_time > $schedule->end_time
        ) {
            return redirect()
                ->route('admin-branch.branch-configuration.timeslots.create')
                ->with('success', 'Tidak bisa menambahkan slot waktu di jam tutup');
        }

        Slot::create([
            'department_id' => $request->department_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        return redirect()
            ->route('admin-branch.branch-configuration.timeslots.create')
            ->with('success', 'Slot waktu ditambahkan');
    }

    public function destroy($id)
    {
        Slot::destroy($id);

        return redirect()
            ->route('admin-branch.branch-configuration.timeslots.index')
            ->with('success', 'Slot waktu layanan dihapus');
    }
}
