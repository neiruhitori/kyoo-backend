<?php

namespace App\Http\Controllers;

use App\Models\FeatureSubscription;
use Illuminate\Support\Facades\Crypt;
use App\Branch;
use App\Appointment;
use App\Models\TVToken;

class AppointmentSignageController extends Controller
{
    public function index($branchId)
    {
        $branch = Branch::where('id', Crypt::decrypt($branchId))
            ->first();
        $TVConfigurationID = $branch->TVConfiguration->id;
        $TVToken = TVToken::where('tv_configuration_id', $TVConfigurationID)->where('token', request()->token)->first();

        if(!$TVToken){
            abort(403);
        }

        $features = FeatureSubscription::with('AdditionalFeature')
            ->where('branch_id', Crypt::decrypt($branchId))
            ->get();

        return view('appointments.monitor', [
            'branch' => $branch,
            'signature' => $branchId,
            'features' => $features,
            'config' => $branch->BranchConfiguration
        ]);
    }

    public function getAppointments($branchId)
    {
        $id = Crypt::decrypt($branchId);

        $data = Appointment::with(['Service', 'Workstation'])
            ->withoutCanceled()
            ->whereHas('Service', function ($query) use ($id) {
                $query->where('branch_id', $id);
            })
            ->whereHas('Service.WorkstationService')
            ->where('date', date('Y-m-d'))
            ->whereNotIn('status', ['end served', 'no show'])
            ->orderBy('created_at')
            ->get();

        return response()->json($data);
    }
}
