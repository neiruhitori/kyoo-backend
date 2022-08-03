<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingWorkstationRepositoryInterface 
{
    public function getReport(Request $request = null);
}