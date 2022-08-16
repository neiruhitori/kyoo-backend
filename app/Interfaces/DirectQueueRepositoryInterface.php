<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface DirectQueueRepositoryInterface 
{
    public function store($data);
    public function getHourlyQueueByDepartment($departmentId, Request $request);
    public function getDailyQueueByDepartment($departmentId, Request $request);
    public function getMonthlyQueueByDepartment($departmentId, Request $request);
}