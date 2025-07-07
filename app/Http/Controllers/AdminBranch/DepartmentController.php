<?php

namespace App\Http\Controllers\AdminBranch;

use App\Log;
use App\Service;
use App\Department;
use App\Models\SubService;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminBranch\StoreDepartment;
use App\Http\Requests\AdminBranch\UpdateDepartment;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        $service_categories = ServiceCategory::whereBranchId(Auth::user()->branch_id)->get();
        $sub_services = SubService::whereBranchId(Auth::user()->branch_id)->get();
        $servicesQuery = Service::whereBranchId(Auth::user()->branch_id);

        if($request->has('filter')){
            if ($request->filter == 'inactive') {
                $servicesQuery->where('is_disable', true);
            } else if($request->filter == 'active') {
                $servicesQuery->where('is_disable', false);
            }
        }else{
             $servicesQuery->where('is_disable', false);
        }

        $services = $servicesQuery->get();

        return view('adminBranch.department.index', [
            'departments' => $departments,
            'service_categories' => $service_categories,
            'services' => $services,
            'sub_services' => $sub_services,
            'filter' => $request->filter ?? 'active'
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
