<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Workstation;
use App\Department;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreWorkstation;
use App\Http\Requests\AdminBranch\UpdateWorkstation;
use Auth;

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
        return view('adminBranch.workstation.index')->withWorkstations($workstations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // for MVP
        if (!Auth::user()->Branch->BranchType->is_premium || count(Auth::user()->Branch->Workstations) > 0) {
            $request->session()->flash('warning', 'Only one workstations can be created!');
            return redirect(route('adminBranch.workstation.index'));
        }
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.workstation.create')->withDepartments($departments);
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
        if (!Auth::user()->Branch->BranchType->is_premium || count(Auth::user()->Branch->Workstations) > 0) {
            $request->session()->flash('warning', 'Only one workstations can be created!');
            return redirect(route('adminBranch.workstation.index'));
        }
        Workstation::create($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Workstation'
        ]);
        $request->session()->flash('success', 'Workstation '.$request->name.' has been inserted!');
        return redirect(route('adminBranch.workstation.index'));
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
        // gate
        if ($workstation->Department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.workstation.edit')->withWorkstation($workstation)->withDepartments($departments);
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
        $request->session()->flash('warning', 'Workstation '.$request->name.' has been updated!');
        return redirect(route('adminBranch.workstation.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Workstation  $workstation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workstation $workstation)
    {
        // gate
        if ($workstation->Department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $workstation->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Workstation'
        ]);
        $request->session()->flash('error', 'Workstation '.$workstation->name.' has been removed!');
        return redirect(route('adminBranch.workstation.index'));
    }
}
