<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Branch;
use App\WorkstationService;
use Illuminate\Http\Request;
use App\Models\WebkioskToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\WebkioskConfiguration;

class KioskController extends Controller
{
    public function login(Request $request)
    {
        $user = null;

        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else if ($request->email) {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Data pengguna atau password tidak sesuai.',
                'status_code' => 400
            ], 400);
        }

        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken('authToken')->accessToken,
            'token_type' => 'Bearer',
            'status_code' => 200,
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json('Successfully Logout');
    }

    public function getWebkioskUI()
    {
        $branch_id = Auth::user()->branch_id;
        $configuration = WebkioskConfiguration::where('branch_id', $branch_id)
                        ->with('layoutConfiguration2')
                        ->with('layoutConfiguration3')
                        ->with('layoutConfiguration4')
                        ->with('layout')
                        ->first();
        $branch = Branch::findOrFail($branch_id);
        $WebkioskConfigurationID = $branch->WebkioskConfiguration->id;
        $WebKioskToken = WebkioskToken::where('webkiosk_configuration_id', $WebkioskConfigurationID)->first();

       return response()->json([
            'user' => Auth::user()->name,
            'role' => Auth::user()->role,
            'config' => $configuration,
            'webkiosk_token' => $WebKioskToken,
       ]);
    }

    public function getService()
    {
        $branch_id = Auth::user()->branch_id;
        $workstationServices = WorkstationService::whereHas('Workstation.WorkstationVct', function ($query) use ($branch_id) {
            $query->whereIn('vct_id', function ($subquery) use ($branch_id) {
                $subquery->select('id')
                    ->from('users')
                    ->where('branch_id', $branch_id)
                    ->whereIn('role', ['cs', 'spv']);
            });
        })->with('Service')->get();
        
        // Hilangkan duplikasi berdasarkan `service_id`
        $uniqueServices = collect($workstationServices)->unique('service_id')->values();
        
        return response()->json([
            'success' => true,
            'message' => 'get all service on branch',
            'data' => $uniqueServices,
        ]);
    }
}
