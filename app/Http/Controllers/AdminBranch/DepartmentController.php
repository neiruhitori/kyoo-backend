<?php

namespace App\Http\Controllers\AdminBranch;

use App\Department;
use App\Service;
use App\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreDepartment;
use App\Http\Requests\AdminBranch\UpdateDepartment;
use Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        $services = Service::whereBranchId(Auth::user()->branch_id)->get();

        return view('adminBranch.department.index', [
            'departments' => $departments,
            'services' => $services
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
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->Departments) > 0) {
            $request->session()->flash('warning', __('Only one department can be created'));
            return redirect(route('admin-branch.branch-configuration.department.index'));
        }
        return view('adminBranch.department.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepartment $request)
    {
        // for MVP
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->Departments) > 0) {
            $request->session()->flash('warning', __('Only one department can be created'));
            return redirect(route('admin-branch.branch-configuration.department.index'));
        }
        Department::create($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Department'
        ]);
        $request->session()->flash('success', __('module.created', ['module' => __('Department'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        // gate
        if ($department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        return view('adminBranch.department.edit', [
            'department' => $department
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepartment $request, Department $department)
    {
        // gate
        if ($department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $department->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Department'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Department'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Department $department)
    {
        // in MVP, can not destroy
        return redirect(route('admin-branch.branch-configuration.department.index'));
        
        // gate
        if ($department->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $department->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Department'
        ]);
        $request->session()->flash('error', __('module.removed', ['module' => __('Department'), 'name' => $department->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }
}
