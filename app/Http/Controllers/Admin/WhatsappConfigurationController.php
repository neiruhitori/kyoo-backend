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

    public function update(Request $request, $id)
    {
        $request->validate([
            'whatsapp_type' => 'required|in:official_wa_branch',
            'api_wa' => ($request->whatsapp_type == 'official_wa_branch') ? 'required' : '',
            'api_token' => ($request->whatsapp_type == 'official_wa_branch') ? 'required' : '',
        ]);

        $branchConfiguration = BranchConfiguration::where('branch_id', $id)->first();

        $branchConfiguration->update([
            'whatsapp_type' => $request->whatsapp_type,
            'api_wa' => $request->api_wa,
            'api_token' => $request->api_token
        ]);

        $request->session()->flash('success', 'Konfigurasi Whatsapp diperbarui');
        return redirect()->back();
    }
}
