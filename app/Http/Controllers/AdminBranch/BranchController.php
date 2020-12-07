<?php

namespace App\Http\Controllers\AdminBranch;

use App\Branch;
use App\BranchType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\Log;
use Countries;
use App\Models\Province;
use App\Http\Requests\AdminBranch\UpdateBranch;
use Auth;
use Storage;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $countries = Countries::getList('en_US');
        $categories = IndustryCategory::all();
        $templates = ScheduleTemplate::all();
        $provinces = Province::all();
        $branch = Branch::find(Auth::user()->branch_id);
        $branchTypes = BranchType::all();
        return view('adminBranch.branch.edit', [
            'branch' => $branch,
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces,
            'branchTypes' => $branchTypes
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranch $request)
    {
        $input = $request->all();
        $branch = Branch::find(Auth::user()->branch_id);
        if (isset($request->logo)) {
            Storage::disk('public')->delete($branch->logo);
            $input['logo'] = Storage::disk('public')->put('branch_logos', $request->logo);
        }

        if (isset($request->photo)) {
            Storage::disk('public')->delete($branch->logo);
            $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        }
        $branch->update($input);
        
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Branch'
        ]);
        $request->session()->flash('warning', 'Branch '.$request->name.' has been updated!');
        return redirect(route('adminBranch.home'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        //
    }
}
