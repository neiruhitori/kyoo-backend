<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class VctController extends Controller
{
    public function getByDepartmentId($id)
    {
        return User::whereHas('WorkstationVct.Workstation', function ($query) use ($id) {
            $query->where('department_id', $id);
        })->get();
    }
}
