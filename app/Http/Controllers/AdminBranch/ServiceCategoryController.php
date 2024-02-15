<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBranch\StoreServiceCategory;
use App\Http\Requests\AdminBranch\UpdateServiceCategory;
use App\Log;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service_categories = ServiceCategory::whereBranchId(Auth::user()->branch_id)->get();

        return view('adminBranch.serviceCategories.index', [
            'service_categories' => $service_categories
        ]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminBranch.serviceCategories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceCategory $request)
    {
        ServiceCategory::create($request->all());

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Service Category'
        ]);

        $request->session()->flash('success', __('module.created', ['module' => __('Service Category'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceCategory $service_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceCategory $service_category)
    {
        // gate
        if ($service_category->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        return view('adminBranch.serviceCategories.edit', [
            'service_category' => $service_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateServiceCategory $request, ServiceCategory $service_category)
    {
        // gate
        if ($service_category->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $service_category->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Service Category'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Service Category'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceCategory  $service_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ServiceCategory $service_category)
    {
        try {
            if ($service_category->branch_id != Auth::user()->branch_id) {
                return redirect(route('unauthorized'));
            }

            $service_category->delete();

            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Remove Service Category'
            ]);

            $request->session()->flash('error', __('module.removed', ['module' => __('Service Category'), 'name' => $service_category->name]));

            return redirect(route('admin-branch.branch-configuration.department.index'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23503]')) {
                return back()->with('error', 'Hapus layanan gagal. Data masih direferensikan dari tabel lain');
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
