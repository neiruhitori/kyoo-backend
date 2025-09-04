<?php

namespace App\Http\Controllers\API;

use App\Models\Regency;
use App\Models\Province;

use App\Models\SGProvince;
use App\Models\SGRegencies;
use App\Models\VNProvinces;
use App\Models\VNRegencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if ($country) {
            $provinces = DB::table('provinces')->where('country', $country)->get();
                return response()->json([
                    'success' => true,
                    'message' => 'get all province',
                    'data' => $provinces
                ]);
        }else{
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
        $regency = Regency::with('province')->find($regency_id);

        return response()->json([
            'success' => true,
            'message' => 'get regencies by id',
            'data' => $regency
        ]);
    }

    public function regencyByProvince($country,$province_id)
    {
        if ($country) {
                $regencies = Regency::where('country', $country)
                                ->where('province_id', $province_id)->get();
                return response()->json([
                    'success' => true,
                    'message' => 'get all regencies',
                    'data' => $regencies
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'country not identified',
                ], 404);
            }
        
    }
}
