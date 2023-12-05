<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TVConfigurationRepositoryInterface
{
    public function GetOneConfigurationByBranchID($branchID);
    public function Upsert($branchID, Request $request);
}
