<?php

namespace App\Http\Controllers\API\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;

class ServiceController extends Controller
{
    private $branch_id = 17;

    public function index(Request $request)
    {
        $limit = $request->limit ?: 10;

        $services = Service::query()->whereHas('WorkstationService')->whereBranchId($this->branch_id);
        $services->when($request->name, function($query) use($request){
            $query->where('name', 'like', "%{$request->name}%");
        });

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch_id',
            'data' => $services->paginate($limit)
        ]);
    }
}
