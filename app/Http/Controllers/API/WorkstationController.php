<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Workstation;

class WorkstationController extends Controller
{
    public function getByDepartmentId($id)
    {
        return Workstation::where('department_id', $id)->get();
    }
}
