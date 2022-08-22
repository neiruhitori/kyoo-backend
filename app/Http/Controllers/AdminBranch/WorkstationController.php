<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Workstation;
use App\Department;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreWorkstation;
use App\Http\Requests\AdminBranch\UpdateWorkstation;
use Illuminate\Support\Facades\Auth;

class WorkstationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workstations = Workstation::whereHas('Department', function($query){
            return $query->whereBranchId(Auth::user()->branch_id);
        })->get();

        return view('adminBranch.workstation.index', [
            'workstations' => $workstations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // for MVP
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->Workstations) > 0) {
            $request->session()->flash('warning', __('Only one workstations can be created!'));
            return redirect(route('admin-branch.branch-configuration.workstation.index'));
        }

        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        
        return view('adminBranch.workstation.create', [
            'departments' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWorkstation $request)
    {
        // for MVP
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->Workstations) > 0) {
            $request->session()->flash('warning', __('Only one workstations can be created'));
            return redirect(route('admin-branch.branch-configuration.workstation.index'));
        }

        // validate max counter
        $totalWorkstations = count(Auth::user()->Branch->Workstations);
        $maxCounter = Auth::user()->Branch->max_counter;
        if ($totalWorkstations >= $maxCounter) {
            $request->session()->flash('error', __('Counter creation has reach the limit'));
            return redirect(route('admin-branch.branch-configuration.workstation.create'))->withInput();
        }

        Workstation::create($request->all());
        
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Workstation'
        ]);
        
        $request->session()->flash('success', __('module.created', ['module' => __('Workstation'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.workstation.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Workstation  $workstation
     * @return \Illuminate\Http\Response
     */
    public function show(Workstation $workstation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Workstation  $workstation
     * @return \Illuminate\Http\Response
     */
    public function edit(Workstation $workstation)
    {
        if ($workstation->Department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        
        return view('adminBranch.workstation.edit', [
            'workstation' => $workstation,
            'departments' => $departments
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Workstation  $workstation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkstation $request, Workstation $workstation)
    {
        // gate
        if ($workstation->Department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $workstation->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Workstation'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Workstation'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.workstation.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Workstation  $workstation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workstation $workstation)
    {
        try {
            if ($workstation->Department->branch_id != Auth::user()->branch_id) {
                return redirect(route('unauthorized'));
            }
    
            $workstation->delete();
            
            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Remove Workstation'
            ]);
            
            $request->session()->flash('error', __('module.removed', ['module' => __('Workstation'), 'name' => $workstation->name]));
            
            return redirect(route('admin-branch.branch-configuration.workstation.index'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23503]')) {
                return back()->with('error', 'Hapus meja gagal. Data masih direferensikan dari tabel lain');
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
