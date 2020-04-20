<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIndustryCategory;
use App\Http\Requests\Admin\UpdateIndustryCategory;
use App\IndustryCategory;
use Illuminate\Http\Request;

use Storage;

class IndustryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = IndustryCategory::all();
       return view('admin.industryCategory.index')->withCategories($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.industryCategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIndustryCategory $request)
    {
        $input = $request->all();
        $input['icon'] = Storage::disk('public')->put('icons', $request->icon);
        IndustryCategory::create($input);
        $request->session()->flash('success', 'Industry Category '.$request->name.' has been added!');
        return redirect(route('admin.industryCategory.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\IndustryCategory  $industryCategory
     * @return \Illuminate\Http\Response
     */
    public function show(IndustryCategory $industryCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\IndustryCategory  $industryCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(IndustryCategory $industryCategory)
    {
        return view('admin.industryCategory.edit')->withCategory($industryCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\IndustryCategory  $industryCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIndustryCategory $request, IndustryCategory $industryCategory)
    {
        $input = $request->all();
        if ($request->icon) {
            Storage::disk('public')->delete($industryCategory->icon);
            $input['icon'] = Storage::disk('public')->put('icons', $request->icon);
        }
        $industryCategory->update($input);
        $request->session()->flash('warning', 'Industry Category '.$request->name.' has been updated!');
        return redirect(route('admin.industryCategory.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\IndustryCategory  $industryCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, IndustryCategory $industryCategory)
    {
        $industryCategory->delete();
        $request->session()->flash('error', 'Industry Category '.$industryCategory->name.' has been removed!');
        return redirect(route('admin.industryCategory.index'));
    }
}
