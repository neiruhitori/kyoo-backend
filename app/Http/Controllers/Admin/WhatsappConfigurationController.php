<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\BranchConfiguration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappConfigurationController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('admin.branch.whatsappConfiguration.index')->withBranches($branches);
    }

    public function show($id)
    {
        $branch = Branch::find($id);

        $data = [
            'branch' => $branch,
            'branch_license' => $branch->BranchType,
            'branch_configuration' => $branch->BranchConfiguration
        ];

        return view('admin.branch.whatsappConfiguration.show', $data);
    }
    public function showGTM($id)
    {
        $branch = Branch::find($id);

        $data = [
            'branch' => $branch,
            'branch_license' => $branch->BranchType,
            'branch_configuration' => $branch->BranchConfiguration
        ];

        return view('admin.branch.whatsappConfiguration.showGTM', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'whatsapp_type' => 'required',
            'api_wa' => ($request->whatsapp_type == 'official_wa_branch') ? 'required' : '',
            'api_token' => ($request->whatsapp_type == 'official_wa_branch') ? 'required' : '',
            'secret_key' => ($request->whatsapp_type == 'wa_kyoo') ? 'required' : '',
        ]);
         $branchConfiguration = BranchConfiguration::where('branch_id', $id)->first();
            
            if ($request->whatsapp_type === 'wa_kyoo') {
                $branchConfiguration->update([
                    'api_token' => $request->secret_key, 
                    'api_wa' => null, 
                ]);
            } elseif ($request->whatsapp_type === 'official_wa_branch') {
                $branchConfiguration->update([
                    'api_token' => $request->api_token,
                    'api_wa' => $request->api_wa,
                ]);
            }

          
            $branchConfiguration->update([
                'whatsapp_type' => $request->whatsapp_type,
            ]);

        $request->session()->flash('success', 'Konfigurasi Whatsapp diperbarui');
        return redirect()->back();
    }
    public function updateGTM(Request $request, $id)
    {
        $request->validate([
            'gtm_script' => 'required',
            'gtm_noscript' => 'required',
        ]);

        $branchConfiguration = BranchConfiguration::where('branch_id', $id)->first();


        $branchConfiguration->gtm_script = $request->gtm_script;
        $branchConfiguration->gtm_noscript = $request->gtm_noscript;
        $branchConfiguration->save();

        $request->session()->flash('success', 'Konfigurasi Google Tag Manager diperbarui');
        return redirect()->back();
    }
}
