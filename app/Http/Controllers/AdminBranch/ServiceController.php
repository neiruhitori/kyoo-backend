<?php

namespace App\Http\Controllers\AdminBranch;

use App\Log;
use App\Service;
use App\Department;
use App\DirectQueue;
use App\BranchConfiguration;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminBranch\StoreService;
use App\Http\Requests\AdminBranch\UpdateService;

class ServiceController extends Controller
{

    private $PREFIX_QUEUE_LIST = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "I", "J", "K", "L", "M", "N", "O", "P");
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::whereBranchId(Auth::user()->branch_id)->get();

        return view('adminBranch.service.index', [
            'services' => $services
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        $service_categories = ServiceCategory::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.service.create', [
            'departments' => $departments,
            'service_categories' => $service_categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        $total_services = Service::where('branch_id', Auth::user()->branch_id)->count();

        if (!Auth::user()->Branch->is_premium && $total_services >= 5) {
            $request->session()->flash('error', __('Batas maksimal 5 layanan telah terlampaui untuk lisensi gratis'));
            return redirect(route('admin-branch.branch-configuration.service.create'));
        }

        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        
        $usedPrefixes = Service::where('branch_id', $input['branch_id'])->pluck('prefix_queue')->toArray();

        foreach ($this->PREFIX_QUEUE_LIST as $prefix) {
            if (!in_array($prefix, $usedPrefixes)) {
                $input['prefix_queue'] = $prefix;
                break;
            }
        }

        Service::create($input);

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Service'
        ]);

        $request->session()->flash('success', __('module.created', ['module' => __('Service'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    private function isAllowConfigPrefix()
    {
        return Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->Branch->hasAccess('Panggilan Suara');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        // gate
        if ($service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $isDirectQueueAndPemiumUser = Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->Branch->BranchType->is_premium;
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        $service_categories = ServiceCategory::whereBranchId(Auth::user()->branch_id)->get();
        $branchConfig = BranchConfiguration::where('branch_id',Auth::user()->branch_id)->first();

        $isOfficialWA = $branchConfig->whatsapp_type === 'official_wa_branch';

        return view('adminBranch.service.edit', [
            'service' => $service,
            'departments' => $departments,
            'service_categories' => $service_categories,
            'prefixQueueList' => $this->PREFIX_QUEUE_LIST,
            'isAllowConfigPrefix' => $this->isAllowConfigPrefix(),
            'isDirectQueueAndPemiumUser' => $isDirectQueueAndPemiumUser,
            'isOfficialWA' => $isOfficialWA,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateService $request, Service $service)
    {
        // gate
        if ($service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        //prevent editing while service is serving queue
        $checkQueue = DirectQueue::whereDate('created_at', now())
                    ->where('service_id', $service->id)
                    ->whereNotIn('status', ['end served','no show'])->exists();

        if ($checkQueue) {
            $request->session()->flash('error', 'Service is still serving queues!');
            return redirect()->back();
        }

        if ($this->isAllowConfigPrefix()) {
            $prefixQueues = Service::where('branch_id', '=', $service->branch_id)
            ->where('id', '!=', $service->id)
            ->pluck('prefix_queue')
            ->toArray();

            $prefixQueues = array_map('trim', $prefixQueues);
            if (in_array($request->prefix_queue, $prefixQueues)) {
                return back()->with('error', 'Edit Layanan gagal. Kustom nomer antrian sudah di pakai pada layanan lain');
            }
        }

        $service->is_show = $request->has('is_show');
        $service->is_show_webkiosk = $request->has('is_show_webkiosk');
        $service->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Service'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Service'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Service $service)
    {
        try {
            if ($service->branch_id != Auth::user()->branch_id) {
                return redirect(route('unauthorized'));
            }

            $service->delete();

            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Remove Service'
            ]);

            $request->session()->flash('error', __('module.removed', ['module' => __('Service'), 'name' => $service->name]));

            return redirect(route('admin-branch.branch-configuration.department.index'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23503]')) {
                return back()->with('error', 'Hapus layanan gagal. Data masih direferensikan dari tabel lain');
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
