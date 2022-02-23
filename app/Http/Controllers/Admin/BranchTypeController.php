<?php

namespace App\Http\Controllers\Admin;

use App\BranchType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreBranchType;
use App\Http\Requests\Admin\UpdateBranchType;

class BranchTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchTypes = BranchType::all();
        return view('admin.branchType.index', [
            'branchTypes' => $branchTypes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.branchType.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBranchType $request)
    {
        BranchType::create($request->all());
        $request->session()->flash('success', __('module.created', ['module' => __('Branch Type'), 'name' => $request->name]));
        return redirect(route('admin.branchType.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BranchType  $branchType
     * @return \Illuminate\Http\Response
     */
    public function show(BranchType $branchType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BranchType  $branchType
     * @return \Illuminate\Http\Response
     */
    public function edit(BranchType $branchType)
    {
        return view('admin.branchType.update', [
            'branchType' => $branchType
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BranchType  $branchType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchType $request, BranchType $branchType)
    {
        $branchType->update($request->all());
        $request->session()->flash('warning', __('module.updated', ['module' => __('Branch Type'), 'name' => $request->name]));
        return redirect(route('admin.branchType.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BranchType  $branchType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BranchType $branchType)
    {
        $branchType->delete();
        $request->session()->flash('error', __('module.removed', ['module' => __('Branch Type'), 'name' => $branchType->name]));
        return redirect(route('admin.branchType.index'));
    }
}
