<?php

namespace App\Http\Controllers\API;

use App\Models\Regency;
use App\Models\Province;

use App\Models\SGProvince;
use App\Models\SGRegencies;
use App\Models\VNProvinces;
use App\Models\VNRegencies;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function provinceByCountry($country)
    {
        switch ($country) {
            case 'Indonesia':
                $provinces = Province::all();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);

            case 'Singapore':
                $provinces = SGProvince::all();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);

            case 'Vietnam':
                $provinces = VNProvinces::all();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'country not identified',
                ], 404);
        }
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

    public function regencyByProvince($country,$province)
    {
        switch ($country) {
            case 'Indonesia':
                $provinces = Regency::where('province_id', $province)->get();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);

            case 'Singapore':
                $provinces = SGRegencies::where('province_id', $province)->get();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);

            case 'Vietnam':
                $provinces = VNRegencies::where('province_id', $province)->get();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'country not identified',
                ], 404);
        }
    }
}
