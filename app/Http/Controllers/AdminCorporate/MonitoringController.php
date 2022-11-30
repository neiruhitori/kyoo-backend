<?php

namespace App\Http\Controllers\AdminCorporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MonitoringController extends Controller
{
    protected MonitoringService $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function index()
    {
        $corporate = Corporate::find(Auth::user()->corporate_id);

        return view('adminCorporate.monitoring', [
            'corporate' => $corporate
        ]);
    }

    public function monitorBranches()
    {
        try {
            $monitorBranches = $this->monitoringService->monitorBranches(Auth::user()->corporate_id);
    
            return response()->json($monitorBranches);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function monitorServices($branchId)
    {
        try {
            $monitor = $this->monitoringService->monitorServices($branchId);
    
            return response()->json($monitor);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
