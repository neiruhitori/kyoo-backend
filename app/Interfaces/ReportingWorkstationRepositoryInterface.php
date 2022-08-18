<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingWorkstationRepositoryInterface 
{
    public function getReport(Request $request = null);
    public function getDailyQueueByWorkstation($id, Request $request);
    public function getMonthlyQueueByWorkstation($id, Request $request);
}