<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ReportingVctRepositoryInterface 
{
    public function getReport(Request $request = null);
}