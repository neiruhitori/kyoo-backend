<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface DirectQueueRepositoryInterface 
{
    public function store($data);
    public function getHourlyQueueByDepartment($id, Request $request);
    public function getHourlyQueueByService($id, Request $request);
    public function getHourlyQueueByWorkstation($id, Request $request);
    public function getHourlyQueueByVct($id, Request $request);
}