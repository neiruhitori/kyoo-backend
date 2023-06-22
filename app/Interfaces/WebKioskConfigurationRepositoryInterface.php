<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface WebKioskConfigurationRepositoryInterface 
{
    public function GetAllLayout();
    public function GetOneConfigurationByBranchID($branchID);
    public function Upsert($branchID, Request $request);
    public function GetOneLayout2ConfigurationByConfigurationID($configurationID);
}