<?php

namespace App\Http\Controllers\AdminBranch;

use Auth;
use App\Log;
use Storage;
use Countries;
use App\Branch;
use App\BranchType;
use App\Models\Province;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\Models\SGProvince;
use App\Models\VNProvinces;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBranch\UpdateBranch;

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
        $branch = Branch::with('BranchToken')->find(Auth::user()->branch_id);
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

    public function profile()
    {
        $data = [
            'branch' => Branch::find(Auth::user()->branch_id),
            'categories' => IndustryCategory::all(),
            'branchTypes' => BranchType::all()
        ];

        return view('adminBranch.branchInfo.profile', $data);
    }

    public function location()
    {
        $country = Auth::user()->Branch->country;

        switch ($country) {
            case 'Indonesia':
                $provinces = Province::all();
                break;

            case 'Singapore':
                $provinces = SGProvince::all();
                break;

            case 'Vietnam':
                $provinces = VNProvinces::all();
                break;
            
            default:
                 $provinces = Province::all();
                break;
            }
        $data = [
            'branch' => Branch::find(Auth::user()->branch_id),
            'provinces' => $provinces
        ];

        return view('adminBranch.branchInfo.location', $data);
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
            Storage::disk('public')->delete($branch->photo);
            $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        }

        $branch->update($input);
        
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Branch'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Branch'), 'name' => $request->name]));
        return back();
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
