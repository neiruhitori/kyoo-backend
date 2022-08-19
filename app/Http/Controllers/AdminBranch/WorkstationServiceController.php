<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Workstation;
use App\WorkstationService;
use App\Service;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreWorkstationService;
use App\Http\Requests\AdminBranch\UpdateWorkstationService;
use Auth;

class WorkstationServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Workstation $workstation)
    {
        return view('adminBranch.workstation.workstationService.index')->withWorkstation($workstation);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Workstation $workstation)
    {
        $services = Service::where([
            'branch_id' => Auth::user()->branch_id,
            'department_id' => $workstation->department_id
        ])->get();

        return view('adminBranch.workstation.workstationService.create')->withWorkstation($workstation)->withServices($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Workstation $workstation, StoreWorkstationService $request)
    {
        $total_same_workstation_services = WorkstationService::where([
            'service_id' => $request->service_id,
            'workstation_id' => $request->workstation_id
        ])->count();
        if ($total_same_workstation_services > 0) {
            $request->session()->flash('error', __('Layanan meja sudah terdaftar. Silahkan pilih layanan lain.'));
            return redirect(route('admin-branch.branch-configuration.workstation.workstation-service.index', $workstation->id));
        }

        $total_workstation_services = WorkstationService::whereHas('Service', function ($query) {
            return $query->where('branch_id', Auth::user()->branch_id);
        })->count();
        if (!Auth::user()->Branch->is_premium && $total_workstation_services >= 5) {
            $request->session()->flash('error', __('Batas maksimal 5 layanan meja telah terlampaui untuk lisensi gratis.'));
            return redirect(route('admin-branch.branch-configuration.workstation.workstation-service.index', $workstation->id));
        }

        WorkstationService::create($request->all());

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Workstation Service'
        ]);

        $request->session()->flash('success', __('Workstation Service has been inserted'));
        return redirect(route('admin-branch.branch-configuration.workstation.workstation-service.index', $workstation->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WorkstationService  $workstationService
     * @return \Illuminate\Http\Response
     */
    public function show(WorkstationService $workstationService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WorkstationService  $workstationService
     * @return \Illuminate\Http\Response
     */
    public function edit(Workstation $workstation, WorkstationService $workstationService)
    {
        // gate
        if ($workstationService->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $services = Service::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.workstation.workstationService.edit')->withWorkstationService($workstationService)->withServices($services);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WorkstationService  $workstationService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkstationService $request, Workstation $workstation, WorkstationService $workstationService)
    {
        // gate
        if ($workstationService->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $workstationService->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Workstation Service'
        ]);
        $request->session()->flash('warning', __('Workstation Service has been updated'));
        return redirect(route('admin-branch.branch-configuration.workstation.workstation-service.index', $workstation->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WorkstationService  $workstationService
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workstation $workstation, WorkstationService $workstationService)
    {
        // gate
        if ($workstationService->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $workstationService->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Workstation Service'
        ]);
        $request->session()->flash('error', __('Workstation Service has been removed'));
        return redirect(route('admin-branch.branch-configuration.workstation.workstation-service.index', $workstation->id));
    }
}
