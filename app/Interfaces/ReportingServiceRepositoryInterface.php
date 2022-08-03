<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingServiceRepositoryInterface 
{
    public function getReport(Request $request = null);
}