<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Province;
use App\Models\Regency;

class RegionController extends Controller
{
    public function allProvince()
    {
        $provinces = Province::all();
        return response()->json([
            'success' => true,
            'message' => 'get all province',
            'data' => $provinces
        ]);
    }

    public function allRegency()
    {
        $regencies = Regency::all();
        return response()->json([
            'success' => true,
            'message' => 'get all regencies',
            'data' => $regencies
        ]);
    }

    public function regencyById($regency_id)
    {
        $regency = Regency::find($regency_id);

        return response()->json([
            'success' => true,
            'message' => 'get regencies by id',
            'data' => $regency
        ]);
    }

    public function regencyByProvince($province)
    {
        $regencies = Regency::where('province_id', $province)->get();
        return response()->json([
            'success' => true,
            'message' => 'get regencies by province',
            'data' => $regencies
        ]);
    }
}
