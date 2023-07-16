<?php

namespace App\Http\Controllers\AdminBranch;

use App\Models\CsActiveMenus;
use App\Models\MenuFeatures;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CSAccessController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $branchID = Auth::user()->branch_id;
        $data = [
            'branch' => Auth::user()->Branch,
            'features' => MenuFeatures::all(),
            'active_menus' => CsActiveMenus::where('branch_id', $branchID)->get()
        ];

        return view('adminBranch.cs.access', $data);
    }


    /**
     * @param Request $request
     * @param $branchID
     * @return RedirectResponse
     */
    public function update(Request $request, $branchID)
    {
        DB::beginTransaction();

        try {
            $csActiveMenus = collect($request->feature_name)->map(function ($feature_id) use ($branchID) {
                return [
                    'branch_id' => $branchID,
                    'feature_id' => $feature_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            })->toArray();

            DB::table('cs_active_menus')->where('branch_id', $branchID)->delete();
            DB::table('cs_active_menus')->insert($csActiveMenus);
            DB::commit();

            $request->session()->flash('success', 'Akses petugas layanan diperbarui');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            $request->session()->flash('error', 'Error Update akses petugas layanan');
            return redirect()->back();
        }
    }

}
