<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingServiceDistributionRepositoryInterface 
{
    public function getReport(Request $request = null);
}