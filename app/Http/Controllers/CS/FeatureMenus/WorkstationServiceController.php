<?php

namespace App\Http\Controllers\CS\FeatureMenus;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBranch\StoreWorkstationService;
use App\Http\Requests\AdminBranch\UpdateWorkstationService;
use App\Log;
use App\Service;
use App\Workstation;
use App\WorkstationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class WorkstationServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $workstationId = Auth::user()->WorkstationVct->workstation_id;
        $workstation = Workstation::with('WorkstationService.Service')
            ->where('id', $workstationId)
            ->first();

        return view('cs.featureMenus.workstationService.index', [
            'workstation' => $workstation
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $workstationId = Auth::user()->WorkstationVct->workstation_id;
        $workstation = Workstation::with('WorkstationService.Service')
            ->where('id', $workstationId)
            ->first();

        $services = Service::where([
            'branch_id' => Auth::user()->branch_id,
            'department_id' => $workstation->department_id
        ])->get();

        return view('cs.featureMenus.workstationService.create', [
            'workstation' => $workstation,
            'services' => $services
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreWorkstationService $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(StoreWorkstationService $request)
    {
        try {
            $total_same_workstation_services = WorkstationService::where([
                'service_id' => $request->service_id,
                'workstation_id' => $request->workstation_id
            ])->count();
            if ($total_same_workstation_services > 0) {
                $request->session()->flash('error', __('Layanan meja sudah terdaftar. Silahkan pilih layanan lain.'));
                return redirect(route('cs.feature-menus.workstation-service.index'));
            }

            $total_workstation_services = WorkstationService::whereHas('Service', function ($query) {
                return $query->where('branch_id', Auth::user()->branch_id);
            })->count();
            if (!Auth::user()->Branch->is_premium && $total_workstation_services >= 5) {
                $request->session()->flash('error', __('Batas maksimal 5 layanan meja telah terlampaui untuk lisensi gratis.'));
                return redirect(route('cs.feature-menus.workstation-service.index'));
            }

            WorkstationService::create($request->all());

            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Insert Workstation Service'
            ]);

            $request->session()->flash('success', __('Workstation Service has been inserted'));
            return redirect(route('cs.feature-menus.workstation-service.index'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param WorkstationService $workstationService
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function edit(WorkstationService $workstationService)
    {
        // gate
        $userBranchID = Auth::user()->branch_id;
        if ($workstationService->Service->branch_id != $userBranchID) {
            return redirect(route('unauthorized'));
        }

        $workstation = Workstation::where('id', $workstationService->workstation_id)
            ->first();
        $services = Service::where([
            'branch_id' => $userBranchID,
            'department_id' => $workstation->department_id
        ])->get();

        return view('cs.featureMenus.workstationService.edit')->withWorkstationService($workstationService)->withServices($services);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateWorkstationService $request
     * @param WorkstationService $workstationService
     * @return Application|Redirector|RedirectResponse
     */
    public function update(UpdateWorkstationService $request, WorkstationService $workstationService)
    {
        try {
            // gate
            if ($workstationService->Service->branch_id != Auth::user()->branch_id) {
                return redirect(route('unauthorized'));
            }

            if ($workstationService->service_id != $request->service_id) {
                return back()->with('error', 'Tolong jangan ganti service yang sudah dibuat');
            }

            $workstationService->update($request->all());
            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Update Workstation Service'
            ]);
            $request->session()->flash('success', __('Workstation Service has been updated'));
            return redirect(route('cs.feature-menus.workstation-service.index'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param WorkstationService $workstationService
     * @return Application|Redirector|RedirectResponse
     */
    public function destroy(Request $request, WorkstationService $workstationService)
    {
        try {
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
            return redirect(route('cs.feature-menus.workstation-service.index'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23503]')) {
                return back()->with('error', 'Hapus layanan gagal. Data masih direferensikan dari tabel lain');
            }

            return back()->with('error', $e->getMessage());
        }
    }

}
