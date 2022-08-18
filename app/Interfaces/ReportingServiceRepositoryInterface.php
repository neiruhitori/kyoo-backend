<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingServiceRepositoryInterface 
{
    public function getReport(Request $request = null);
    public function getDailyQueueByService($id, Request $request);
    public function getMonthlyQueueByService($id, Request $request);
}