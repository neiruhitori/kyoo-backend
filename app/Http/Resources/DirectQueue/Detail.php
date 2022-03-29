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
        $curr_queue = Onsite::where('workstation_service_id', $this->workstation_service_id)
            ->whereDate('created_at', date('Y-m-d', strtotime($this->created_at)))
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->first();
        $branch = $this->WorkstationService->Service->Branch;
        $directQueue = $this;
 
        return [
            'id' => $directQueue->id,
            'date' => $directQueue->created_at,
            'branch_id' => $directQueue->WorkstationService->Service->Branch->id,
            'branch_name' => $directQueue->WorkstationService->Service->Branch->name,
            'service_id' => $directQueue->WorkstationService->Service->id,
            'service_name' => $directQueue->WorkstationService->Service->name,
            'total_waiting' => Onsite::whereServiceId($this->service_id)->whereStatus('waiting')->whereDate('created_at', date('Y-m-d'))->count(),
            'currently_attending' => Onsite::whereServiceId($this->service_id)->whereStatus('served')->whereDate('created_at', date('Y-m-d'))->count(),
            'name' => $directQueue->name,
            'phone' => $directQueue->phone,
            'queue_no' => $directQueue->queue_no,
            'is_liked' => $directQueue->is_liked,
            'rating' => $directQueue->rating,
            'status' => $directQueue->status,
            'current_queue' => $curr_queue->queue_no,
            'industry_category' => $branch->IndustryCategory->name
        ];
    }
}
