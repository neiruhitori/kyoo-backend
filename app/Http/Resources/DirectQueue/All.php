<?php

namespace App\Http\Resources\DirectQueue;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DirectQueue;

class All extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'workstation_service_id' => $this->id,
            'service' => $this->Service,
            'total_queue' => DirectQueue::whereWorkstationServiceId($this->id)->whereCreatedAt(date('Y-m-d'))->count(),
            'total_waiting' => DirectQueue::whereWorkstationServiceId($this->id)->whereStatus('waiting')->whereCreatedAt(date('Y-m-d'))->count(),
        ];
    }
}
