<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingDepartmentRepositoryInterface 
{
    public function getReport(Request $request = null);
    public function getDailyQueueByDepartment($id, Request $request);
    public function getMonthlyQueueByDepartment($id, Request $request);
}