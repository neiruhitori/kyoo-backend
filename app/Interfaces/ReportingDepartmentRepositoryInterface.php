<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingDepartmentRepositoryInterface 
{
    public function getReport(Request $request = null);
}