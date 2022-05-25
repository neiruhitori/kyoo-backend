<?php

namespace App\Http\Resources\DirectQueue;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DirectQueue as Onsite;

class Detail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $curr_queue = Onsite::where('service_id', $this->service_id)
            ->whereDate('created_at', date('Y-m-d', strtotime($this->created_at)))
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->first();
        $branch = $this->Service->Branch;

        $total_remaining_queue = Onsite::where('service_id', $this->service_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->whereIn('status', ['waiting', 'requeue', 'served'])
            ->where('id', '<', $this->id)
            ->count();
 
        return [
            'id' => $this->id,
            'date' => $this->created_at,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'service_id' => $this->service_id,
            'service_name' => $this->Service->name,
            'total_waiting' => Onsite::whereServiceId($this->service_id)
                ->whereStatus('waiting')
                ->whereDate('created_at', date('Y-m-d'))
                ->count(),
            'currently_attending' => Onsite::whereServiceId($this->service_id)
                ->whereStatus('served')
                ->whereDate('created_at', date('Y-m-d'))
                ->count(),
            'name' => $this->name,
            'phone' => $this->phone,
            'queue_no' => $this->queue_no,
            'is_liked' => $this->is_liked,
            'rating' => $this->rating,
            'status' => $this->status,
            'current_queue' => $curr_queue ? $curr_queue->queue_no : $request->queue_no,
            'industry_category' => $branch->IndustryCategory->name,
            'total_remaining_queue' => $total_remaining_queue
        ];
    }
}
