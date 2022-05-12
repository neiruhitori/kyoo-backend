<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DirectQueue;
use App\Appointment;
use App\Models\Exhibition;

class SearchQueueController extends Controller
{
    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $onsite = DirectQueue::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
            ->latest()
            ->first();
        if ($onsite) {
            return redirect('customer/' . $onsite->Service->branch_id . '/onsite/booking-status/' . $onsite->id);
        }

        $appointment = Appointment::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
            ->latest()
            ->first();
        if ($appointment) {
            return redirect('customer/' . $appointment->Slot->Service->branch_id . '/appointment/booking-status/' . $appointment->id);
        }

        $exhibition = Exhibition::whereRaw('UPPER(booking_code) = ?', [strtoupper($request->booking_code)])
            ->latest()
            ->first();
        if ($exhibition) {
            return redirect('customer/' . $exhibition->Slot->Service->branch_id . '/exhibition/booking-status/' . $exhibition->id);
        }
        
        return redirect()->back()->withErrors(['booking_code' => 'Antrian tidak ditemukan']);
    }
}
