<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Mail\CS\AppointmentOnsiteCreatedMail;
use App\Models\AppointmentOnsite;
use App\Service;
use App\Slot;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentOnsiteController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?: date('Y-m-d');

        $appointment_onsites = AppointmentOnsite::where('date', $date)
                                                ->where('is_used', false)
                                                ->join('services', 'appointment_onsites.service_id', '=', 'services.id')
                                                ->orderBy('date')
                                                ->orderBy('services.name')
                                                ->orderBy('start_time')
                                                ->select('appointment_onsites.*')
                                                ->get();

        return view('adminBranch.appointmentOnsites.index', [
            'appointment_onsites' => $appointment_onsites,
            'date' => $date,
            'success' => true
        ]);
    }

    public function editSlot(AppointmentOnsite $appointmentOnsite)
    {
        $services = Service::where('branch_id', Auth::user()->branch_id)->get();

        return view('adminBranch.appointmentOnsites.editSlot', [
            'appointment_onsite' => $appointmentOnsite,
            'services' => $services
        ]);
    }

    public function update(AppointmentOnsite $appointmentOnsite, Request $request)
    {
        $branch = Auth::user()->Branch;
        $slot = Slot::find($request->slot_id);

        if ($this->isAppointmentSlotFull($request->slot_id, $request->date)) {
            return redirect()->back()->with('error', 'Sesi Appointment Tidak Tersedia');
        }

        $appointmentOnsite->update([
            'service_id' => $request->service_id,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'date' => $request->date,
            'slot_id' => $request->slot_id,
        ]);

        if (
            $appointmentOnsite->phone &&
            $branch &&
            $branch->is_premium &&
            $branch->BranchConfiguration->wa_notification != false &&
            $branch->BranchConfiguration->whatsapp_type == 'official_wa_branch'
        ) {
            $appointmentOnsite->sendAppointmentOnsiteCreatedNotification($appointmentOnsite);
        }

        try {
            Mail::to($appointmentOnsite->email)->send(new AppointmentOnsiteCreatedMail($appointmentOnsite));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Pengiriman email gagal, mohon cek koneksi internet Anda.');
        }

        return redirect()->back()->with('success', 'Slot Waktu diperbaharui');
    }

    public function getSlots(Request $request)
    {
        $slots = Slot::where('service_id', $request->service_id)->where('day', $request->day)->get();

        return response()->json([
            'slots' => $slots,
            'status' => 'success',
        ]);
    }

    public function isAppointmentSlotFull($slotId, $date)
    {
        $slot = Slot::find($slotId);
        $formattedDate = date('Y-m-d', strtotime($date));

        $totalTodayAppointmentsBySlot = AppointmentOnsite::where([
            'slot_id' => $slotId,
            'date' => $formattedDate
        ])->count();

        return $totalTodayAppointmentsBySlot >= $slot->max_slots;
    }
}
