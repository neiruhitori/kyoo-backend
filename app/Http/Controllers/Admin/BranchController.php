<?php

namespace App\Http\Controllers\Admin;

use App\Branch;
use App\IndustryCategory;
use App\ScheduleTemplate;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBranch;
use Illuminate\Http\Request;
use Countries;
use App\Models\Province;

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
        $branches = Branch::where('status', 'verified')->get();
        return view('admin.branch.index')->withBranches($branches);
    }

    /**
     * Display a listing of Verifying Branch
     */

     public function verifyList()
     {
         return view('admin.branch.verify');
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Countries::getList('en_US');
        $categories = IndustryCategory::all();
        $templates = ScheduleTemplate::all();
        $provinces = Province::all();
        return view('admin.branch.create', [
            'countries' => $countries,
            'categories' => $categories,
            'templates' => $templates,
            'provinces' => $provinces
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBranch $request)
    {
        // create branch
        $input = $request->all();
        $input['logo'] = Storage::disk('public')->put('branch_logos', $request->logo);
        $input['photo'] = Storage::disk('public')->put('branch_photos', $request->photo);
        $input['status'] = 'verified';
        $branch = Branch::create($input);

        // create admin branch
        $user = User::create([
            'branch_id' => $branch->id,
            'name' => $input['admin_name'],
            'email' => $input['admin_email'],
            'password' => $input['admin_password'],
            'phone' => $input['admin_phone'],
            'role' => 'admin_branch'
        ]);
        $request->session()->flash('success', 'Branch '.$request->name.' has been inserted!');
        return redirect(route('admin.branch.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return view('admin.branch.show')->withBranch($branch);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Branch $branch)
    {
        $branch->delete();
        $request->session()->flash('error', 'Branch '.$branch->name.' has been suspended!');
        return redirect(route('admin.branch.index'));
    }
}
