<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exhibition as ExhibitionModel;

class Exhibition extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $booking = ExhibitionModel::find($this['id']);
        $currently_attending = ExhibitionModel::where('slot_id', $booking->slot_id)
            ->where('date', $booking->date)
            ->where('status', 'served')
            ->first();
        $queue_total = ExhibitionModel::where('slot_id', $booking->slot_id)
            ->where('date', $booking->date)
            ->where('status', 'book')
            ->orderBy('queue_order', 'desc')
            ->first();
        $curr_queue = ExhibitionModel::where('slot_id', $booking->slot_id)
            ->where('date', $booking->date)
            ->where('status', 'book')
            ->first();

        return [
            'id' => $booking->id,
            'branch_id' => $booking->Slot->Service->Branch->id,
            'branch_name' => $booking->Slot->Service->Branch->name,
            'service_name' => $booking->Slot->Service->name,
            'service_id' => $booking->Slot->Service->id,
            'status' => $booking->status,
            'date' => $booking->date,
            'start_time' => $booking->Slot->start_time,
            'end_time' => $booking->Slot->end_time,
            'timezone' => $booking->Slot->Service->Branch->timezone,
            'booking_code' => $booking->booking_code,
            'industry_category' => $booking->Slot->Service->Branch->IndustryCategory->name,
            'name' => $booking->name,
            'phone' => $booking->phone,
            'email' => $booking->email,
            'rating' => $booking->rating,
            'is_liked' => $booking->is_liked,
            'queue_no' => (int) $booking->queue_order,
            'total_queue' => $queue_total ? (int) $queue_total['queue_order'] : 0,
            'current_queue' => $curr_queue ? (int) $curr_queue['queue_order'] : 0,
            'total_waiting' => ExhibitionModel::where('slot_id', $booking->slot_id)->where('date', $booking->date)->where('queue_order', '<', $booking->queue_order)->whereIn('status', ['book', 'check in'])->get()->count(),
            'currently_attending' => isset($currently_attending) ? intval($currently_attending->queue_order) : 0,
        ];
    }
}
