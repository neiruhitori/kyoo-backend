<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exhibition;
use Crypt;

class ExhibitionController extends Controller
{
    public function status($id)
    {
        $id = Crypt::decrypt($id);
        $queue = Exhibition::find($id);
        $currently_attending = Exhibition::select('queue_order')
            ->where('slot_id', $queue->slot_id)
            ->where('date', $queue->date)
            ->where('status', 'end served')
            ->first();
        $total_waiting = Exhibition::where('slot_id', $queue->slot_id)
            ->where('date', $queue->date)
            ->where('queue_order', '<', $queue->queue_order)
            ->where('status', 'book')
            ->get()
            ->count();

        // user can not see the queue status after +1 day of appointment
        $expired_date = date('Y-m-d', strtotime('+1 day', strtotime($queue->date)));
        $date_now = date('Y-m-d');
        if ($date_now > $expired_date) {
            return redirect('https://www.kyoo.id/cloud/');
        } 
        
        return view('exhibitionStatus', [
            'queue' => $queue,
            'total_waiting' => $total_waiting,
            'currently_attending' => isset($currently_attending) ? $currently_attending->queue_order : 0
        ]);
    }
}
