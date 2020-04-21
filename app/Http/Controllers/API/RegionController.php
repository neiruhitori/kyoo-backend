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
