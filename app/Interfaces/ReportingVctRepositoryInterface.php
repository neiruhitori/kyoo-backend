<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingVctRepositoryInterface 
{
    public function getReport(Request $request = null);
    public function getDailyQueueByVct($id, Request $request);
    public function getMonthlyQueueByVct($id, Request $request);
}