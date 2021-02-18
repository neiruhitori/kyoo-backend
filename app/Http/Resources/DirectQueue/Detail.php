<?php

namespace App\Http\Resources\DirectQueue;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DirectQueue;

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
        $directQueue = $this;
        return [
            'id' => $directQueue->id,
            'date' => $directQueue->created_at,
            'branch_name' => $directQueue->WorkstationService->Service->Branch->name,
            'service_name' => $directQueue->WorkstationService->Service->name,
            'total_waiting' => DirectQueue::whereWorkstationServiceId($this->workstation_service_id)->whereStatus('waiting')->where('queue_no', '<', $directQueue->queue_no)->whereDate('created_at', date('Y-m-d'))->count(),
            'name' => $directQueue->name,
            'phone' => $directQueue->phone,
            'queue_no' => $directQueue->queue_no,
            'is_liked' => $directQueue->is_liked,
            'rating' => $directQueue->rating,
        ];
    }
}
